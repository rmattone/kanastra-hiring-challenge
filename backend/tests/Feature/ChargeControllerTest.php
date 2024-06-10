<?php

namespace Tests\Feature;

use App\DataView\ImportedFilesDataView;
use App\Enums\ImportedFileEnum;
use Tests\TestCase;
use Mockery;
use App\Helpers\ImportedFilesHelper;
use App\Http\Controllers\ChargeController;
use App\Http\Requests\Charges\StoreChargeRequest;
use App\Jobs\ProcessInvoicesJob;
use App\Models\ImportedFiles;
use App\Services\ImportedFilesService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;

class ChargeControllerTest extends TestCase
{
    protected $importedFilesHelper;
    protected $service;
    protected $dataView;
    protected $storeChargeRequest;

    public function setUp(): void
    {
        parent::setUp();
        $this->dataView = new ImportedFilesDataView();
        $this->service = Mockery::mock(ImportedFilesService::class);
        $this->importedFilesHelper = Mockery::mock(ImportedFilesHelper::class);
        $this->storeChargeRequest = new StoreChargeRequest();
    }

    public function testListSuccess()
    {
        // Simula a resposta da requisição normal
        $expectedResponse = [
            [
                "id" => 6,
                "name" => "filename",
                "description" => "abc",
                "status" => 4,
                "path" => "6664accd8e1bb-2024-06-08-19-11-09.csv",
                "created_at" => "08/06/2024 19:11:09"
            ]
        ];
        $expectedJson = json_encode($expectedResponse);

        $this->importedFilesHelper->shouldReceive('list')->once()->andReturn($expectedResponse);
        $controller = new ChargeController($this->importedFilesHelper);
        $response = $controller->list();

        $this->assertJsonStringEqualsJsonString($expectedJson, $response->getContent());
    }


    public function testListError()
    {
        $expectedResponse = [
            'message' => 'Some error'
        ];

        $this->importedFilesHelper->shouldReceive('list')->once()->andThrow(new \Exception('Some error'));

        $controller = new ChargeController($this->importedFilesHelper);
        $response = $controller->list();

        $this->assertEquals($expectedResponse, $response->getData(true));
    }

    public function testView()
    {
        $file = new ImportedFiles();
        $file->id = 1;
        $file->name = 'file1';
        $file->description = 'desc1';
        $file->status = 1;
        $file->path = 'path/to/file1';
        $file->created_at = '2023-01-01 00:00:00';

        $response = $this->dataView->view($file);

        $expected = [
            'id' => 1,
            'name' => 'file1',
            'description' => 'desc1',
            'status' => 1,
            'path' => 'file1',
            'created_at' => Carbon::parse('2023-01-01 00:00:00')->format('d/m/Y H:i:s')
        ];

        $this->assertEquals($expected, $response);
    }

    
    public function testStoreSuccess()
    {
        $filePath = base_path('tests/Fixtures/test.zip');
        $uploadedFile = new UploadedFile($filePath, 'test.zip', 'application/zip', null, true);
    
        $storeChargeRequest = new StoreChargeRequest();
        $storeChargeRequest->merge([
            'name' => 'test',
            'description' => 'test description',
            'csv_file' => $uploadedFile
        ]);
    
        $importedFile = new ImportedFiles();
        $importedFile->id = 1;
        $importedFile->name = 'test';
        $importedFile->description = 'test description';
        $importedFile->status = ImportedFileEnum::PENDING;
        $importedFile->path = 'new-file-name.csv';
    
        $this->importedFilesHelper
            ->shouldReceive('saveFile')
            ->once()
            ->with($storeChargeRequest)
            ->andReturn($importedFile);
    
        $this->service
            ->shouldReceive('store')
            ->withArgs(function ($name, $description, $fileName, $status) use ($importedFile) {
                return $name === $importedFile->name &&
                       $description === $importedFile->description &&
                       $fileName === $importedFile->path &&
                       $status === ImportedFileEnum::PENDING;
            })
            ->andReturn($importedFile);
    
        Queue::fake();
        Queue::shouldReceive('connection')
            ->once()
            ->andReturnSelf(); 
            Queue::shouldReceive('push')
            ->once()
            ->with(Mockery::type(ProcessInvoicesJob::class));
    
        $controller = new ChargeController($this->importedFilesHelper, $this->service);
        $response = $controller->store($storeChargeRequest);
    
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($importedFile->id, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals($importedFile->name, $responseData['name']);
    }
}
