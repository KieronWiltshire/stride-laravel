<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthAccessTokensTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('oauth_access_tokens', function (Blueprint $table) {
      $table->string('id', 100)->primary();
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->integer('client_id')->unsigned();
      $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
      $table->string('name')->nullable();
      $table->text('scopes')->nullable();
      $table->boolean('revoked');
      $table->timestamps();
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
    Schema::dropIfExists('oauth_access_tokens');
  }
}
