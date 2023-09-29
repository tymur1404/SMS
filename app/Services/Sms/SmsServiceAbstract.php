<?php

namespace App\Services\Sms;

use App\Models\GateQueue;

class SmsServiceAbstract implements SmsServiceInterface
{
    public function sendSms(GateQueue $params, string $message): ?string
    {
        return '';
    }

    public static function strSplitUnicode(string $str, int $chunkLength = 0): array
    {
        if ($chunkLength <= 0) {
            return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        }

        $chunks = [];
        $len = mb_strlen($str, 'UTF-8');

        for ($i = 0; $i < $len; $i += $chunkLength) {
            $chunks[] = mb_substr($str, $i, $chunkLength, 'UTF-8');
        }

        return $chunks;
    }

    private function buildRequestHeaders(string $path, string $host, string $userAgent, string $sendData): array
    {
        return [
            "POST $path HTTP/1.0",
            "Host: $host",
            "User-Agent: $userAgent",
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($sendData),
            "Connection: Close",
        ];
    }

    protected function sendPostRequest(string $url,
                                   array $postData,
                                   int $port = 80,
                                   string $userAgent = 'PHPPost/1.0'): string
    {
        $urlInfo = parse_url($url);
        $scheme = ($urlInfo['scheme'] === 'https') ? 'ssl://' : '';
        $host = $urlInfo['host'];
        $path = $urlInfo['path'] ?? '/';
        $query = isset($urlInfo['query']) ? '?' . $urlInfo['query'] : '';

        $sendData = http_build_query($postData);

        $headers = $this->buildRequestHeaders("POST $path$query", $host, $userAgent, $sendData);

        $out = implode("\r\n", $headers) . "\r\n\r\n" . $sendData;

        $fp = @fsockopen($scheme . $host, $port);

        if (!$fp) {
            return false;
        }

        fwrite($fp, $out);

        $response = '';
        while (!feof($fp)) {
            $response .= fgets($fp, 128);
        }

        fclose($fp);

        [, $content] = explode("\r\n\r\n", $response, 2);

        return $content;
    }


}
