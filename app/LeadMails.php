<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadMails extends Model
{
    //
    function countSent(){
        return 0;
    }

    function countRejected(){
        return 0;
    }

    function countSentLast24(){
        return 0;
    }

    public static function countReceivedLast24(){
        return 0;
    }

    function countRejectedLast24(){
        return 0;
    }
}
