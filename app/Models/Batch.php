<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Batch Model
 * 
 * Represents a batch of stock additions with automatically generated batch numbers.
 * Each batch is created when an administrator adds stock to products.
 * 
 * Batch numbers follow the format DDMMYY-N where:
 * - DD: Two-digit day (01-31)
 * - MM: Two-digit month (01-12)
 * - YY: Two-digit year (00-99)
 * - N: Sequential counter that increments for each batch on the same date
 * 
 * Example: 120321-1 represents the first batch created on March 12, 2021
 * 
 * @property int $batch_id Primary key
 * @property string $batch_number Unique batch identifier in format DDMMYY-N
 * @property int $user_id ID of the administrator who created the batch
 * @property \Carbon\Carbon $created_at Timestamp when batch was created
 * @property \Carbon\Carbon $updated_at Timestamp when batch was last updated
 */
class Batch extends Model
{
    public $primaryKey = 'batch_id';

    protected $fillable = [
        'batch_number',
        'user_id'
    ];

    /**
     * Get the batch products associated with this batch.
     * 
     * A batch can contain multiple products with their quantities.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batchProducts() {
        return $this->hasMany(BatchProduct::class, 'batch_id', 'batch_id');
    }

    /**
     * Get the products that reference this batch.
     * 
     * Products store a reference to their most recent batch for tracking purposes.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products() {
        return $this->hasMany(Product::class, 'batch_id', 'batch_id');
    }

    /**
     * Get the user who created this batch.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
