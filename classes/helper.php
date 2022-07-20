<?php

/**
 * Description
 *
 * @author  Kaushtuv Gurung (kaushtuv.gurung@kineo.com.au)
 * @date    2022-07-20 
 * @package block_kineo_user_profile
 */
 
namespace block_kineo_user_profile;


class helper {

    public static function get_tenant_profile_fields($tenantid = 0) {
        global $DB;
        
        if(!self::check_profilefield_segregation() || !$tenantid) {
            return $DB->get_records('user_info_field', null, 'sortorder ASC');
        }

        return $DB->get_records_sql(
            "SELECT uif.* 
               FROM {user_info_field} uif
               JOIN {local_tenantsupport_profilefields} ltpf 
                 ON uif.id = ltpf.field_id 
              WHERE ltpf.tenant_id = :tenantid", ['tenantid' => $tenantid]
        );
    }

    public static function get_tenant_profile_field_tree($tenantid = 0) {
        $profile_fields = self::get_tenant_profile_fields($tenantid);

        if(empty($profile_fields)) {
            return [];
        }
        $tree = [];
        foreach ($profile_fields as $field) {
            $tree['custom_field_' . $field->shortname] = $field;
        }
        return $tree;
    }

    public static function check_profilefield_segregation() {
        global $CFG;
    
        if(file_exists("$CFG->dirroot/local/tenantsupport/version.php")) {
            return get_config('local_tenantsupport', 'profilefieldsegregation');
        } 
        return false;
    }
}