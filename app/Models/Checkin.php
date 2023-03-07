<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Actions\Checkin\AssociatePersonToCheckin;
use App\Notifications\NewCheckinNotification;
use App\Actions\GenerateHypervergeURLLink;
use Illuminate\Database\Eloquent\Model;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Illuminate\Support\Arr;
use App\Traits\HasData;

class Checkin extends Model
{
    use HasFactory;
    use HasUuids;
//    use HasData;

    protected $fillable = ['longitude', 'latitude', 'url', 'uri', 'data'];

    protected $primaryKey = 'uuid';

    protected $casts = [
        'data' => 'array'
    ];

    protected $appends = [
        'QRCodeURI', 'IdType', 'IdNumber', 'IdFullName', 'IdImageUrl', 'IdBirthdate'
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function person(): MorphTo
    {
        return $this->morphTo();
    }

    public static function createAutoAssociateAgent(User $agent): self
    {
        $checkin = Checkin::make();
        $checkin->agent()->associate($agent);
        $checkin->save();

        return $checkin;
    }

    public function updateRemoteGeneratedURL(): self
    {
        $url = GenerateHypervergeURLLink::run($this->getAttribute('uuid'));
        if ($url) {
            $this->setAttribute('url', $url);
            $this->save();
        }

        return $this;
    }

    public function updateContactPersonFromMobile(string $mobile = null): self
    {
        if ($mobile) {
            AssociatePersonToCheckin::run($this, $mobile);
        }

        return $this;
    }

    public function notifyAgent(): self
    {
        $this->agent?->notify(new NewCheckinNotification($this->getAttribute('url')));

        return $this;
    }

    public function getQRCodeURIAttribute(): ?string
    {
        $uri = null;
        if ($this->getAttribute('url')) {
            $qr = QrCode::create($this->url);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            $uri = $result->getDataUri();
        }

        return $uri;
    }

    public function getWorkflowIdAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.workflow_id'));
    }

    public function getApplicationStatusAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.application_status'));
    }

    public function getCountryAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.country'));
    }

    public function getIdImageUrlAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_image_url'));
    }

    public function getIdTypeAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_type'));
    }

    public function getIdNumberAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_number'));
    }

    public function getIdExpiryAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_expiry'));
    }

    public function getIdFullNameAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_full_name'));
    }

    public function getIdBirthdateAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_birthdate'));
    }

    public function getIdAddressAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_address'));
    }

    public function getIdGenderAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_gender'));
    }

    public function getIdNationalityAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.id_nationality'));
    }

    public function getFaceImageUrlAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.face_image_url'));
    }

    public function getFaceCheckStatusAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.face_check_status'));
    }

    public function getFaceCheckDetailsAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.face_check_details'));
    }

    public function getFaceIdMatchStatusAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.face_id_match_status'));
    }

    public function getFaceIdMatchDetailsAttribute()
    {
        return Arr::get($this->getAttribute('data'), config('domain.hyperverge.mapping.face_id_match_details'));
    }
}
