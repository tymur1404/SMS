<?php

namespace App\Repositories;

use App\Models\GateQueue;
use App\Models\PsPymess;
use Illuminate\Database\Eloquent\Model;

class PsPymessRepository
{
    public static function sendLiraSms($sessionId)
    {
        PsPymess::insert([
            'column_name' => '<root><type>lira</type><message>send_sms|' . $sessionId . '#</message></root>'
        ]);
    }

    public static function sendLongGsmSms(Model $params, string $message): void
    {
        $messageChunks = self::strSplitUnicode($message, 70);
        foreach ($messageChunks as $value) {
            self::sendShortGsmSms($params, $value);
            sleep(1);
        }
    }

    public static function sendShortGsmSms(Model $params, string $message): void
    {
        PsPymess::insert([
            'column_name' => self::buildGsmCommandXml($params, $message)
        ]);
    }

    private static function buildGsmCommandXml(Model $params, string $message): string
    {
        return '<root>
                    <type>gsm_command</type>
                    <pid>' . self::sanitizeValue($params->server_pid) . '</pid>
                    <gsm_data>
                        {"command_type":"ami",
                        "command":"dongle sms ' .
                        self::sanitizeValue($params->post_login) . ' +' .
                        self::sanitizeValue($params->phone) . ' ' .
                        self::sanitizeValue($message) . '",
                        "device":"' . self::sanitizeValue($params->post_login) . '"}
                    </gsm_data>
                </root>';
    }

    private static function sanitizeValue($value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

}
