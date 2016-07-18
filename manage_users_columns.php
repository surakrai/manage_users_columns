function mimo_modify_user_table( $column ) {

  unset( $column['posts'] );
  $column['user_order'] = 'สั่งซื้อสำเร็จ';

  return $column;

}

add_filter( 'manage_users_columns', 'mimo_modify_user_table' );


function mimo_modify_user_table_row( $value, $column_name, $user_id ) {

  switch ( $column_name ) {

    case 'user_order' :

      $total_amount = $total_item = 0;

      $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
        'post_type'   => 'shop_order',
        'post_status' => 'wc-completed'
      ) );

      if ( $customer_orders ) :

        foreach ( $customer_orders as $customer_order ) :

          $order = wc_get_order( $customer_order );
          $total_item = $total_item + $order->get_item_count();
          $total_amount = $total_amount + $order->get_total();

        endforeach;
        
        return sprintf(
          '%s ครั้ง %s ชิ้น %s<br><a href="%s" target="_blank" >ดูเพิ่มเติม &rarr;</a>',
          count($customer_orders),
          $total_item,
          wc_price( $total_amount ),
          esc_url( add_query_arg( array( 'post_status' => 'wc-completed', 'post_type' => 'shop_order', '_customer_user' => $user_id ), admin_url( 'edit.php' ) ) )
        );

      else :

        return 'ยังไม่มีการสั่งซื้อ';

      endif;

      break;

    default:

  }

  return $value;

}

add_filter( 'manage_users_custom_column', 'mimo_modify_user_table_row', 10, 3 );
