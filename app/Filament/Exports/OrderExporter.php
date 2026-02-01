<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('customer_id'),
            ExportColumn::make('client_id'),
            ExportColumn::make('agent_id'),
            ExportColumn::make('product_id'),
            ExportColumn::make('service_id'),
            ExportColumn::make('customer_name'),
            ExportColumn::make('customer_phone'),
            ExportColumn::make('customer_email'),
            ExportColumn::make('order_reference'),
            ExportColumn::make('amount'),
            ExportColumn::make('currency'),
            ExportColumn::make('status'),
            ExportColumn::make('source'),
            ExportColumn::make('items'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
