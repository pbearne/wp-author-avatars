<?php
/**
 * Author Avatars List
 * User: Paul Bearne
 * Date: 2014-10-25
 * Time: 11:02 AM
 */


class BuddyPressSupport {
	/**
     * @var array
     */
    protected static $profiles_field_list = array();

	/**
     * @return array
     */
    protected static function list_profiles_fields(){
        if( ! empty( self::$profiles_field_list ) ){
            return self::$profiles_field_list;
        }

        global $wpdb;

        //ttdo:  try this  https://buddypress.org/support/topic/how-to-get-list-of-xprofile-filds/#post-227700

        $bp_profile_fields = $wpdb->get_results( "SELECT name FROM `{$wpdb->prefix}bp_xprofile_fields`" );

        $fields = array();
        foreach( $bp_profile_fields as $ob ){
            $id = 'bp_' . str_replace(" ", "_", $ob->name);
            $fields[$id] = __('BP profile: ', 'author-avatars' ). $ob->name;
        }
        self::$profiles_field_list = $fields;
        return  self::$profiles_field_list;

    }

	/**
     * @param $fields
     *
     * @return array
     */
    public static function filter_profiles_fields( $fields ){
         return array_merge( $fields, self::list_profiles_fields() );
     }

	/**
     * @param $display_extra
     * @param $user
     *
     * @return string
     */
    public static function get_profile_outputs( $display_extra, $user ){

        $out = '';
        foreach( $display_extra as $name ){
            $args = array(
                'field'   => str_replace( '_', ' ', str_replace('bp_', '', $name ) ), // Field name or ID.
                'user_id' => $user->user_id
            );
         //   $out .=   bp_get_profile_field_data( $args  );
            $profile_field_data = bp_get_profile_field_data( $args  );
            $out .=  sprintf( apply_filters( 'aa_user_display_extra_template', '<div class="extra %s">%s</div>', $args ,$profile_field_data ),$name, $profile_field_data  );
            //$out .=  '<div class="extra '. $name . '">' . $profile_field_data . '</div>';
        }

        return $out;
    }

}

add_filter( 'AA_render_field_display_options', 'BuddyPressSupport::filter_profiles_fields' );
add_filter( 'aa_user_display_extra', 'BuddyPressSupport::get_profile_outputs', 10 , 2 );


/**
 * Example filter
 *
 * @param $html
 * @param $args
 * @param $value
 *
 * @return mixed
 */
function display_extra_template( $html, $args , $value ){
   if( 'color' == $args['field'] ){
       $html = str_replace( 'class=', 'style="background-color:' . $value . '" class=', $html );
   }
   return  $html;
}
add_filter( 'aa_user_display_extra_template', 'display_extra_template', 10 , 3 );
