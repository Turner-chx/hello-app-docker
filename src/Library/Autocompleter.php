<?php


namespace App\Library;


final class Autocompleter
{
    public static function articleAutocomplete(): array
    {
        return [
            'reference',
            'designation',
            'ean',
        ];
    }

    public static function sourceAutocomplete(): array
    {
        return [
            'name',
        ];
    }

    public static function userAutocomplete(): array
    {
        return [
            'firstName',
            'lastName',
            'login',
            'email',
        ];
    }

    public static function dealerAutocomplete(): array
    {
        return [
            'email',
            'name',
            'dealerCode',
        ];
    }

    public static function natureSettingAutocomplete(): array
    {
        return [
            'setting',
            'codeDivalto',
        ];
    }










    public static function categoryAutocomplete(): array
    {
        return [
            'id',
            'googleId',
            'name',
        ];
    }

    public static function cartAutocomplete(): array
    {
        return [
            'secret',
            'user',
        ];
    }

    public static function familyBrandAutocomplete(): array
    {
        return [
            'id',
            'title',
            'name',
        ];
    }

    public static function brandAutocomplete(): array
    {
        return [
            'id',
            'title',
            'name',
        ];
    }

    public static function familyAutocomplete(): array
    {
        return [
            'id',
            'title',
            'name',
        ];
    }

    public static function typeProductAutocomplete(): array
    {
        return [
            'type',
            'codeLama'
        ];
    }

    public static function gammeAutocomplete(): array
    {
        return [
            'gamme',
        ];
    }

    public static function addressAutocomplete(): array
    {
        return [
            'id',
            'company',
            'lastname',
            'firstname',
            'phone',
            'city',
            'country',
        ];
    }

    public static function productAutocomplete(): array
    {
        return [
            'sku',
            'name',
            'ean',
            'supplierProducts.supplierSku',
            'supplierProducts.supplier'
        ];
    }

    public static function orderAutocomplete(): array
    {
        return [
            'id',
            'carrierCode',
            'deliveryNumber',
            'marketplaceReference',
            'promoCode',
            'customerInformation'
        ];
    }

    public static function supplierAutocomplete(): array
    {
        return [
            'name',
        ];
    }

    public static function printerGroupAutocomplete(): array
    {
        return [
            'name',
        ];
    }

    public static function featureAutocomplete(): array
    {
        return [
            'name',
        ];
    }

    public static function printerAutocomplete(): array
    {
        return [
            'name',
            'title',
        ];
    }

    public static function groupingAutocomplete(): array
    {
        return [
            'name',
            'title',
        ];
    }

    public static function sliderTilesAutocomplete(): array
    {
        return [
            'name',
            'title'
        ];
    }
}