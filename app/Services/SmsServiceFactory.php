<?php

namespace App\Services;

use App\Services\Sms\{DevinoteleService,
    DevinoteleViberService,
    EsputnikService,
    KcellSmsService,
    MainSmsService,
    MobakService,
    Sms100Service,
    SmsFlyService,
    SmsServiceInterface,
    AtomService,
    CmfSellService,
    OkitokiService,
    StreamTelecomSmsService
};
use App\Services\Sms\Protech\{Protech1Service, Protech4Service};

class SmsServiceFactory
{
    const TURBOSMS = 'turbosms.ua';
    const STREAM_TELECOM = 'stream_telecom';
    const SMSC = 'smsc.ru';
    const MAINSMS = 'mainsms';
    const MOBAK = 'mobak';
    const SMS_1000 = '1000sms';
    const ESPUTNIK = 'esputnik';
    const DEVINOTELE_VIBER_SMS = 'devinotele_viber_sms';
    const DEVINOTELE_SMS = 'devinotele_sms';
    const SMS_SOFTLINE = 'softline';
    const ATOM = 'atom';
    const KCELL = 'kcell';
    const SMS_FLY = 'sms_fly';
    const CMFCELL = 'cmfcell.com.ua';
    const OKITOKI = 'okitoki';
    const PROTECH1 = 'protech1';
    const PROTECH4 = 'protech4';

    public static function createService($schemaName): SmsServiceInterface
    {
        return match ($schemaName) {

            self::ATOM => new AtomService(),
            self::CMFCELL => new CmfSellService(),
            self::OKITOKI => new OkitokiService(),
            self::PROTECH1 => new Protech1Service(),
            self::PROTECH4 => new Protech4Service(),
            self::STREAM_TELECOM => new StreamTelecomSmsService(),
            self::MAINSMS => new MainSmsService(),
            self::MOBAK => new MobakService(),
            self::SMS_1000 => new Sms100Service(),
            self::ESPUTNIK => new EsputnikService(),
            self::DEVINOTELE_VIBER_SMS => new DevinoteleViberService(),
            self::DEVINOTELE_SMS => new DevinoteleService(),
            self::KCELL => new KcellSmsService(),
            self::SMS_FLY => new SmsFlyService(),
            self::TURBOSMS => app('TurboSmsSoapClient'),
            self::SMS_SOFTLINE => app('SoftlineSoapClient'),
            self::SMSC => app('SMSCSoapClient'),
            default => null,
        };


    }
}
