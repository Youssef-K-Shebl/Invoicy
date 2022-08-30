<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAttachment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoices()
    {
        $this->belongsTo(Invoice::class);
    }
}
