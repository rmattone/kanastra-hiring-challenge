<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('governmentId');
            $table->string('email');
            $table->decimal('debtAmount', 8, 2);
            $table->date('debtDueDate');
            $table->string('debtId', 191); // Ajuste o comprimento para 191
            $table->unsignedBigInteger('importedFileId');
            $table->timestamps();

            $table->foreign('importedFileId')->references('id')->on('imported_files')->onDelete('cascade');
            // $table->unique('debtId_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
