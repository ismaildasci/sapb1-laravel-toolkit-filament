<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Exporters;

use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class SapB1Exporter
{
    /**
     * @return list<string>
     */
    abstract public function getHeaders(): array;

    /**
     * @return iterable<int, list<string|int|float|null>>
     */
    abstract public function getRows(): iterable;

    abstract public function getFilename(): string;

    public function export(): StreamedResponse
    {
        $filename = $this->getFilename();

        return Response::streamDownload(function (): void {
            /** @var resource $handle */
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $this->getHeaders());

            foreach ($this->getRows() as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
