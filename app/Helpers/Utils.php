<?php

namespace App\Helpers;

use App\Shop\ShopStatus;
use App\Transfert;
use App\SupportRequest;

class Utils
{
    public static function transaction_status($status)
    {
        switch ($status) {
            case ShopStatus::PAYMENT_SUCCESS:
                $status_text = "Validé";
                break;
            case ShopStatus::PAYMENT_FAIL:
                $status_text = "Echec";
                break;
            case ShopStatus::PAYMENT_ERROR:
            default:
                $status_text = "Erreur";
                break;
        }

        return $status_text;
    }

    public static function transfert_status($status)
    {
        switch ($status) {
            case Transfert::IN_PROGRESS:
                $status_text = "En cours";
                break;
            case Transfert::OK_SQL:
            case Transfert::OK_API:
                $status_text = "Terminée";
                break;
            case Transfert::REFUND:
                $status_text = "Remboursé";
                break;
            case Transfert::FAIL:
            default:
                $status_text = "Echoué";
                break;
        }

        return $status_text;
    }

    public static function support_request_status($status, $english = [])
    {
        switch ($status) {
            case SupportRequest::OPEN:
                $status_text = "Ouvert";
                $status_text_english = "Open";
                break;
            case SupportRequest::CLOSE:
                $status_text = "Fermé";
                $status_text_english = "Close";
                break;
            case SupportRequest::WAIT:
                $status_text = "En attente";
                $status_text_english = "Wait";
                break;
            default:
                $status_text = "Erreur";
                $status_text_english = "Error";
                break;
        }
        return $english == true ? $status_text_english : $status_text;
    }
    
    public static function format_price($price, $delimiter = ' ')
    {
        if(!is_numeric($price))
            return "-";
        else
            return number_format(round(floor($price), 0, PHP_ROUND_HALF_DOWN), 0, ",", $delimiter);
    }
}
