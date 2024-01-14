<?php 
    // Control core classes for avoid errors
    if(class_exists('CSF')){
        // Set a unique slug identifier for your theme options
        $prefix = 'metabox_options';
        // Create options
        CSF::createMetabox($prefix, array(
            'title' => 'Additional information',
            'post_type' => 'post'
        ));

        // Create section
        CSF::createSection( $prefix, array(
            'title' => 'Student Information',
            'fields' => array(
                array(
                    'id'    => 'first_name',
                    'type'  => 'text',
                    'title' => 'First Name',
                    'placeholder'   => 'Enter First Name',
                ),
                array(
                    'id'    => 'last_name',
                    'type'  => 'text',
                    'title' => 'Last Name',
                    'placeholder'   => 'Enter Last Name',
                ),
                array(
                    'id'    => 'email_address',
                    'type' => 'email',
                    'title' => 'Email',
                    'placeholder'   => 'Enter Email',
                ),
                array(
                    'id'    => 'phone_number',
                    'type'  => 'text',
                    'title' => 'Phone Number',
                    'placeholder'   => 'Enter Phone Number',
                ),
                array(
                    'id'    => 'class_name',
                    'type'  => 'select',
                    'title' => 'Class Name',
                    'placeholder'   => 'Select Class Name',
                    'options' => array(
                        'class_1'   => 'Class 1',
                        'class_2'   => 'Class 2',
                        'class_3'   => 'Class 3',
                        'class_4'   => 'Class 4',
                        'class_5'   => 'Class 5',
                    ),
                    'default'   => 'class_1'
                    ),
            )
        ));
        CSF::createSection($prefix, array(
            'title' => 'Textarea Filed',
            'fields' => array(
                array(
                    'id'    => 'opt_textarea',
                    'type'  => 'textarea',
                    'title' => 'Message',
                ),
            )
        ));

    }
?>