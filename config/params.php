<?php

return [
    'site_title' => 'Loqman',
    'role_ids'=>['Admin'=>'1', 'Users'=>'2', 'Expert'=>'3', 'Guestuser'=> '4'],
    'role_names'=>['1'=>'Admin','2'=>"Users", '3'=>'Expert','4'=>'Guestuser'],
    'status' => ['pending' => '0', 'active' => '1', 'inactive'=>'2'],
    'status_arr' => ['inactive' => '0', 'active' => '1'],
    'status_name_arr' => ['1' => 'Active', '0' => 'Inactive'],
    'setting_id' => 1,
    'mail_username' => 'hharley216@gmail.com',
    'admin_default_image' => 'public/images/user.jpg',
    'file_save_path' => 'public',
    'admin_page_length' => 10,
    'setting_field_arr'=> ['admin_commission','search_in_radius' ],
    'success_status' => 200,
    'validation_status' => 400,
    'error_status' => 500,
];

