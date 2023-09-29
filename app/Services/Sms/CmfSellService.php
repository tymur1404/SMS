<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class CmfSellService extends SmsServiceAbstract
{
    const URL = 'smsc.cmfcell.com.ua';
    const PORT = 80;
    private array $cmfResult = [];

    public function sendSms(GateQueue $params, string $message): ?string
    {
        $postString = $this->buildXmlRequest($params, $message);

        $httpResponse = $this->sendXmlRequest($postString);

        $result = $this->parseXmlResponse($httpResponse);

        return $this->formatResult($result);
    }

    private function buildXmlRequest(GateQueue $params, string $message): string
    {
        $postString =
        '<packet version="1.0">
            <auth username="' . $params->login . '" password="' . $params->password . '"/>
            <command name="SendMessage">
                <message type="UnicodeSms">
                    <from>' . $params->senderName . '</from>
                    <sendDate></sendDate>
                    <data>' . $message . '</data>
                    <recipients>
                        <recipient address="+' . $params->recipientPhone . '">
                        </recipient>
                    </recipients>
                </message>
            </command>
        </packet>';

        return $postString;
    }

    private function sendXmlRequest($postString): string
    {
        $fp = fsockopen(self::URL, self::PORT, $errNum, $errMsg, 30) or die("Socket-openfailed--error: " . $errNum . " " . $errMsg);
        fwrite($fp, "POST /sections/service/xmlpost/v1/default.aspx HTTP/1.0\r\n");
        fwrite($fp, "Host: oki-toki.ua\r\n");
        fwrite($fp, "Content-type: text/xml \r\n");
        fwrite($fp, "Content-length: " . strlen($postString) . " \r\n");
        fwrite($fp, "Content-transfer-encoding: text \r\n");
        fwrite($fp, "Connection: close\r\n\r\n");
        fwrite($fp, $postString);

        $httpResponse = '';
        while (!feof($fp)) {
            $httpResponse .= fgets($fp, 128);
        }

        fclose($fp);

        return $httpResponse;
    }

    private function parseXmlResponse(string $httpResponse): array
    {
        $result = [];
        $currentElement = null;
        $currentData = '';

        [, $content] = explode("\r\n\r\n", $httpResponse, 2);

        $XMLparser = xml_parser_create();
        xml_set_element_handler($XMLparser, function ($parser, $name, $attrs) use (&$result, &$currentElement) {
            $currentElement = $name;
        }, function ($parser, $name) use (&$result, &$currentElement, &$currentData) {
            if ($currentElement) {
                $result[$currentElement] = $currentData;
                $currentElement = null;
                $currentData = '';
            }
        });
        xml_set_character_data_handler($XMLparser, function ($parser, $str) use (&$currentData) {
            $currentData .= $str;
        });

        xml_parse($XMLparser, $content);
        xml_parser_free($XMLparser);

        return $result;
    }


    private function formatResult(array $result): string
    {
        $id = "RESULT - " . $result['RESULT'] . "; MessageID - " . $result['MESSAGEID'];

        return $id;
    }
}
