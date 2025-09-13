<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceParcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if parcel relationship is loaded
        if (!$this->parcel) {
            return [
                'customer_name'     => 'N/A',
                'zone'              => 'N/A',
                'status'            => $this->parcel_status ?? 'N/A',
                'date'              => 'N/A',
                'cash_collection'   => $this->collected_amount ?? 0,
                'delivery_charge'   => $this->total_charge_amount ?? 0
            ];
        }

        $status = '';

        if( $this->parcel_status == \App\Enums\ParcelStatus::RETURN_TO_COURIER ):
            $status .= trans("parcelStatus.24").', ';
        endif;

        if($this->parcel->partial_delivered == \App\Enums\BooleanStatus::YES): 
            $status .= trans("parcelStatus.".\App\Enums\ParcelStatus::PARTIAL_DELIVERED ); 
        else:
            if( $this->parcel->status != \App\Enums\ParcelStatus::RETURN_TO_COURIER ):
                $status .= __('parcelStatus.'.$this->parcel->status);
            endif;
        endif;
   
        return [
            'customer_name'     => $this->parcel->customer_name ?? 'N/A',
            'zone'              => $this->parcel->customer_address ?? 'N/A',
            'status'            => $status ?: trans("parcelStatus.".$this->parcel->status),
            'date'              => $this->parcel->delivered_date ? Carbon::parse($this->parcel->delivered_date)->format('d-m-Y H:i A') : 'N/A',   
            'cash_collection'   => $this->collected_amount ?? 0,
            'delivery_charge'   => $this->total_charge_amount ?? 0
        ];

    }
}
