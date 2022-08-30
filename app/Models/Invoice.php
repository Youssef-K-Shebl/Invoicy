<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $guarded = [];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoicesDetails::class);
    }

    public function invoiceAttachments()
    {
        return $this->hasMany(InvoiceAttachment::class);
    }



}
