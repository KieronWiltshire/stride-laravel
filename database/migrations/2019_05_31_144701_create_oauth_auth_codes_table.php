<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAuthCodesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('oauth_auth_codes', function (Blueprint $table) {
      $table->string('id', 100)->primary();
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->integer('client_id')->unsigned();
      $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
      $table->text('scopes')->nullable();
      $table->boolean('revoked');
      $table->dateTime('expires_at')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('oauth_auth_codes');
  }
}
