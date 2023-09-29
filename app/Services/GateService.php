<?php

namespace App\Services;
use App\Event\Event;
use App\Jobs\SendSmsJob;
use App\Models\Contact;
use App\Models\UserField;
use App\Models\WorkingGate;
use App\Repositories\GateLinkToCompanyRepository;
use App\Repositories\GateQueueRepository;
use App\Repositories\GateRepository;


class GateService
{
    const WEBSITE = 'oki-toki.ua';
    const PATH = '/srv/www/htdocs/okitoki';
    const PROCESS_PARAMS_DELIMITER = ',';
    const PROCESS_PARAMS_PREFIX = 'param';
    const PREPARE_MESSAGE_SEARCH = '##';

    public function __construct(protected int $gateId){}

    private function processParams(string $message, string $params): string
    {
        $processedParams = explode(self::PROCESS_PARAMS_DELIMITER, $params);

        foreach ($processedParams as $key => $param) {
            if ($param !== '') {
                $message = str_replace(self::PROCESS_PARAMS_PREFIX . ($key + 1), $param, $message);
            }
        }

        return $message;
    }
    public function prepareMessage(string $message, string $params, int $id): string
    {
        $msg = $this->processParams($message, $params);

        $contactFields = UserField::all()->pluck('field_name', 'id')->toArray();

        $contact = Contact::find($id);

        foreach ($contactFields as $key => $name) {
            $msg = str_ireplace(self::PREPARE_MESSAGE_SEARCH . $name, $contact->$key, $msg);
        }

        return $msg;
    }

    function process(): void
    {
        WorkingGate::create([ 'gate_id' => $this->gateId]);
        $gate = GateRepository::getGate($this->gateId);

        $gateQueue = GateQueueRepository::getGateQueue($this->gateId);
        foreach ($gateQueue as $queueItem) {
            event(new Event($gateQueue->schema_name, self::WEBSITE, self::PATH));

            $gateLinkPrice = GateLinkToCompanyRepository::getPriceFromGateLinkToCompany($queueItem, $this->gateId);

            $provider = $this->getSmsService($gate->type_abbr);
            if ($provider) {
                $message = $this->prepareMessage($queueItem->sms_text,
                    $queueItem->params,
                    $queueItem->crm_id);

                SendSmsJob::dispatch($provider, $queueItem, $gateLinkPrice, $gate, $message);
            }
        }

        WorkingGate::destroy($this->gateId);
    }

    private function getSmsService(string $schemaName): Sms\SmsServiceInterface|null
    {
        return SmsServiceFactory::createService($schemaName);
    }
}
