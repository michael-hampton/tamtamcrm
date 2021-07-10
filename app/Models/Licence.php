<?php

namespace App\Models;


class Licence
{
    public function generateLicense($suffix = null) {
        // Default tokens contain no "ambiguous" characters: 1,i,0,o
        if(isset($suffix)){
            // Fewer segments if appending suffix
            $num_segments = 3;
            $segment_chars = 6;
        }else{
            $num_segments = 4;
            $segment_chars = 5;
        }
      
        $tokens = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $license_string = '';
      
        // Build Default License String
        for ($i = 0; $i < $num_segments; $i++) {
            $segment = '';
            for ($j = 0; $j < $segment_chars; $j++) {
                $segment .= $tokens[rand(0, strlen($tokens)-1)];
            }
            $license_string .= $segment;
            if ($i < ($num_segments - 1)) {
                $license_string .= '-';
            }
        }
      
        // If provided, convert Suffix
        if(isset($suffix)){
            if(is_numeric($suffix)) {   // Userid provided
                $license_string .= '-'.strtoupper(base_convert($suffix,10,36));
            }else{
                $long = sprintf("%u\n", ip2long($suffix),true);
                if($suffix === long2ip($long) ) {
                    $license_string .= '-'.strtoupper(base_convert($long,10,36));
                }else{
                    $license_string .= '-'.strtoupper(str_ireplace(' ','-',$suffix));
                }
            }
        }
      
        return $license_string;
    }
}
