<?php

if (! defined('ABSPATH')) {
    exit;
}

use StoutLogic\AcfBuilder\FieldsBuilder;

/**
 * ACF Builder : booking_data + provider_profile
 */
add_action('acf/init', function () {

    // ====== BOOKING FIELDS ======
    $booking = new FieldsBuilder('booking_data', [
        'title' => 'Booking data',
    ]);

    $booking
        ->setLocation('post_type', '==', 'booking');

    $booking
        ->addUser('booking_provider', [
            'label'         => 'Provider',
            'role'          => ['um_entertainer'],
            'return_format' => 'id',
            'required'      => 1,
        ])
        ->addUser('booking_customer', [
            'label'         => 'Customer',
            'role'          => ['customer'],
            'return_format' => 'id',
            'required'      => 1,
        ])
        ->addDatePicker('booking_date', [
            'label'          => 'Date',
            'display_format' => 'Y-m-d',
            'return_format'  => 'Y-m-d',
            'required'       => 1,
        ])
        ->addTimePicker('booking_time', [
            'label'          => 'Time',
            'display_format' => 'H:i',
            'return_format'  => 'H:i',
            'required'       => 0,
        ])
        ->addNumber('booking_duration', [
            'label'         => 'Duration (minutes)',
            'default_value' => 60,
            'min'           => 0,
            'step'          => 15,
        ])
        ->addNumber('booking_price', [
            'label' => 'Price',
            'min'   => 0,
            'step'  => 1,
        ])
        ->addNumber('booking_order_id', [
            'label'        => 'Order ID',
            'instructions' => 'WooCommerce order ID linked to this booking.',
        ])
        ->addSelect('booking_status', [
            'label'         => 'Booking Status',
            'choices'       => [
                'pending'   => 'Pending',
                'confirmed' => 'Confirmed',
                'cancelled' => 'Cancelled',
            ],
            'default_value' => 'pending',
            'ui'            => 1,
            'required'      => 1,
        ])
        ->addTextarea('booking_notes', [
            'label'     => 'Notes',
            'new_lines' => 'br',
        ]);

    acf_add_local_field_group($booking->build());

    // ====== PROVIDER PROFILE FIELDS ======
    $providerProfile = new FieldsBuilder('provider_profile', [
        'title' => 'Provider profile',
    ]);

    $providerProfile
        ->setLocation('user_role', '==', 'um_entertainer');

    $providerProfile
       
        ->addText('provider_stage_name', [
            'label'        => 'Stage name',
            'instructions' => 'Displayed name for customers.',
            'required'     => 1,
        ])
        ->addText('provider_genre', [
            'label'        => 'Genre (legacy text)',
            'instructions' => 'e.g. DJ, Cover band, Acoustic, Stand-up.',
        ])

  
        ->addEmail('provider_public_email', [
            'label'        => 'Public email',
            'instructions' => 'Email shown on the entertainer profile.',
        ])
        ->addText('provider_public_phone', [
            'label'        => 'Public phone',
            'instructions' => 'Phone number shown on the entertainer profile.',
        ])


        ->addNumber('provider_price_min', [
            'label'   => 'Price from',
            'prepend' => '$',
            'min'     => 0,
            'step'    => 1,
        ])
        ->addNumber('provider_price_max', [
            'label'   => 'Price to',
            'prepend' => '$',
            'min'     => 0,
            'step'    => 1,
        ])
        ->addNumber('provider_price_per_set', [
            'label'        => 'Price per set',
            'prepend'      => '$',
            'min'          => 0,
            'step'         => 1,
            'instructions' => 'Standard price per performance set.',
        ])
        ->addNumber('provider_hourly_rate', [
            'label'        => 'Hourly rate',
            'prepend'      => '$',
            'min'          => 0,
            'step'         => 1,
            'instructions' => 'Used for cards and price/hour filters.',
        ])

     
        ->addText('provider_city', [
            'label' => 'City',
        ])
        ->addText('provider_country', [
            'label' => 'Country',
        ])
        ->addNumber('provider_band_members_count', [
            'label' => 'Number of band members',
            'min'   => 0,
            'step'  => 1,
        ])
        ->addNumber('provider_stages_performed', [
            'label' => 'Number of stages performed',
            'min'   => 0,
            'step'  => 1,
        ])
        ->addDatePicker('provider_joined_date', [
            'label'          => 'Date joined',
            'display_format' => 'd F, Y',
            'return_format'  => 'Y-m-d',
        ])


        ->addTextarea('provider_description', [
            'label'     => 'About performer',
            'new_lines' => 'br',
        ])

        ->addGallery('provider_gallery', [
            'label' => 'Gallery',
        ])
        ->addUrl('provider_video_url', [
            'label'        => 'Video URL',
            'instructions' => 'Link to promo video (YouTube/Vimeo).',
        ])

    
        ->addTaxonomy('provider_entertainment_categories', [
            'label'         => 'Entertainment Category(ies)',
            'taxonomy'      => 'ustage_entertainment_category',
            'field_type'    => 'multi_select',
            'add_term'      => 1,
            'return_format' => 'id',
        ])
        ->addTaxonomy('provider_entertainment_genres', [
            'label'         => 'Entertainment Genre(s)',
            'taxonomy'      => 'ustage_entertainment_genre',
            'field_type'    => 'multi_select',
            'add_term'      => 1,
            'return_format' => 'id',
        ])

     
        ->addTrueFalse('provider_sound_system_provided', [
            'label'        => 'Do they provide sound system?',
            'ui'           => 1,
            'instructions' => 'Show "Yes/No" in the profile.',
        ])
        ->addNumber('provider_sound_system_fee', [
            'label'        => 'Sound system fee',
            'prepend'      => '$',
            'min'          => 0,
            'step'         => 1,
            'instructions' => 'If sound system is provided, specify the fee.',
        ])

   
        ->addFile('provider_stage_plot_file', [
            'label'        => 'Stage Plot',
            'instructions' => 'Upload Stage Plot PDF.',
            'return_format' => 'array',
        ])
        ->addFile('provider_hospitality_rider_file', [
            'label'        => 'Hospitality Rider',
            'instructions' => 'Upload Hospitality Rider PDF.',
            'return_format' => 'array',
        ])
        ->addFile('provider_technical_rider_file', [
            'label'        => 'Technical Rider',
            'instructions' => 'Upload Technical Rider PDF.',
            'return_format' => 'array',
        ])

   
        ->addNumber('provider_travel_fee', [
            'label'        => 'Travel fee (meals, hotel, mileage)',
            'prepend'      => '$',
            'min'          => 0,
            'step'         => 1,
        ])
        ->addRepeater('provider_preferred_travel_locations', [
            'label'        => 'Preferred travel locations',
            'layout'       => 'row',
            'button_label' => 'Add location',
        ])
        ->addText('city', [
            'label' => 'City',
        ])
        ->addText('region', [
            'label' => 'Region/State',
        ])
        ->addText('country', [
            'label' => 'Country',
        ])
        ->endRepeater()
        ->addCheckbox('provider_preferred_event_types', [
            'label'   => 'Preferred event types',
            'choices' => [
                'festival'      => 'Festival',
                'private_party' => 'Private party',
                'outdoor'       => 'Outdoor',
                'wedding'       => 'Wedding',
            ],
            'layout'  => 'horizontal',
        ])

        ->addSelect('provider_availability_status', [
            'label'         => 'Availability status',
            'choices'       => [
                'available'   => 'Available',
                'unavailable' => 'Unavailable',
            ],
            'default_value' => 'available',
            'ui'            => 1,
        ])

        ->addRepeater('provider_social_links', [
            'label'        => 'Social links',
            'button_label' => 'Add social link',
            'layout'       => 'table',
        ])
        ->addText('label', [
            'label' => 'Label (e.g. Instagram)',
        ])
        ->addUrl('url', [
            'label' => 'URL',
        ])
        ->endRepeater();

    acf_add_local_field_group($providerProfile->build());
});
