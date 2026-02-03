<?php

use App\Models\User;
use App\Models\MaintenanceReport;
use App\Mail\MaintenanceReportMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    Storage::fake('public');
    Mail::fake();
});

test('pdf is generated when completing maintenance report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $response = $this->post(route('maintenance.complete', $report));

    $response->assertRedirect();

    $report->refresh();

    expect($report->status)->toBe('sent')
        ->and($report->pdf_path)->not->toBeNull()
        ->and($report->sent_at)->not->toBeNull();

    Storage::disk('public')->assertExists($report->pdf_path);
});

test('pdf filename includes website name and date', function () {
    $report = MaintenanceReport::factory()->draft()->create([
        'maintenance_date' => Carbon::parse('2025-12-04'),
    ]);

    $this->post(route('maintenance.complete', $report));

    $report->refresh();

    $expectedFilename = 'wartungsbericht_' . $report->website->name . '_2025-12-04.pdf';

    expect($report->pdf_path)->toContain('wartungsbericht_')
        ->and($report->pdf_path)->toContain('2025-12-04');
});

test('email is sent when completing maintenance report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $this->post(route('maintenance.complete', $report));

    Mail::assertSent(MaintenanceReportMail::class, function ($mail) use ($report) {
        return $mail->hasTo($report->website->client->email);
    });
});

test('email contains pdf attachment', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $this->post(route('maintenance.complete', $report));

    Mail::assertSent(MaintenanceReportMail::class, function ($mail) {
        $attachments = $mail->attachments();
        return count($attachments) > 0;
    });
});

test('can download pdf from completed report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    $this->post(route('maintenance.complete', $report));

    $response = $this->get(route('reports.download', $report));

    $response->assertStatus(200)
        ->assertHeader('Content-Type', 'application/pdf');
});

test('can resend email for completed report', function () {
    $report = MaintenanceReport::factory()->sent()->create([
        'pdf_path' => 'reports/test-report.pdf',
    ]);

    Storage::disk('public')->put($report->pdf_path, 'fake pdf content');

    $response = $this->post(route('reports.resend', $report));

    $response->assertRedirect(route('reports.show', $report))
        ->assertSessionHas('success');

    Mail::assertSent(MaintenanceReportMail::class, function ($mail) use ($report) {
        return $mail->hasTo($report->website->client->email);
    });
});

test('cannot download pdf if file does not exist', function () {
    $report = MaintenanceReport::factory()->sent()->create([
        'pdf_path' => 'reports/non-existent.pdf',
    ]);

    $response = $this->get(route('reports.download', $report));

    $response->assertStatus(404);
});

test('report status changes to sent after completion', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    expect($report->status)->toBe('draft');

    $this->post(route('maintenance.complete', $report));

    $report->refresh();

    expect($report->status)->toBe('sent');
});

test('sent_at timestamp is set when completing report', function () {
    $report = MaintenanceReport::factory()->draft()->create();

    expect($report->sent_at)->toBeNull();

    $this->post(route('maintenance.complete', $report));

    $report->refresh();

    expect($report->sent_at)->not->toBeNull()
        ->and($report->sent_at)->toBeInstanceOf(Carbon::class);
});

test('cannot complete already sent report', function () {
    $report = MaintenanceReport::factory()->sent()->create();

    $response = $this->post(route('maintenance.complete', $report));

    $response->assertRedirect()
        ->assertSessionHas('warning');
});
