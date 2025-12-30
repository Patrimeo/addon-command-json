<?php

namespace Patrimeo\AddonCommandJson;


use Patrimeo\Contracts\DTO\Transaction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Patrimeo\Contracts\AssetTransactions;
use Patrimeo\Contracts\Errors\AddonError;
use Patrimeo\Contracts\Enums\TransactionType;
use Patrimeo\Contracts\DTO\TransactionCollection;

class CommandJsonService implements AssetTransactions
{
    protected ?string $command;

    public function __construct(array $attributes)
    {
        $this->command = $attributes['command'] ?? null;
        if (empty($this->command)) {
            throw new AddonError(__('Command is required'), null);
        }
    }

    public static function getFields(): array
    {
        return [
            'command' => TextInput::make('command')
                ->label(__('Command')),
        ];
    }

    public static function getSettingFields(): ?Section
    {
        return Section::make(__('Command Json Integration'))
            ->schema([]);
    }



    public function getTransactions(): TransactionCollection
    {
        $output = [];
        $returnCode = 0;

        \exec($this->command, $output, $returnCode);
        if ($returnCode !== 0) {
            throw new AddonError(__('Command failed with return code ' . $returnCode), null);
        }

        try {
            $json = json_decode(implode("\n", $output));
        } catch (\Exception $e) {
            throw new AddonError(__('Invalid JSON format'), null, $e->getMessage());
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AddonError(__('JSON decode error: ' . json_last_error_msg()), null);
        }

        $transactions = new TransactionCollection();
        foreach ($json as $transactionData) {
            $transactionData = (array) $transactionData;
            try {
                $transaction = new Transaction(
                    type: TransactionType::from($transactionData['type']),
                    date: new \DateTimeImmutable($transactionData['date']),
                    source: $transactionData['source'],
                    source_quantity: $transactionData['source_quantity'],
                    destination: $transactionData['destination'],
                    destination_quantity: $transactionData['destination_quantity'],
                    comment: $transactionData['comment'],
                );
            } catch (\Exception $e) {
                throw new AddonError(__('Failed to create transaction: ' . $e->getMessage()), null, $e->getMessage());
            }
            $transactions->addTransaction($transaction);
        }

        return $transactions;
    }
}
