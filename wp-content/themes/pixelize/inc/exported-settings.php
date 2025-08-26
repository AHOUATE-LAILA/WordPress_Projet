<?php

return array (
  'front-header' => 
  array (
    'navigation' => 
    array (
      'props' => 
      array (
        'showTopBar' => false,
        'sticky' => true,
        'overlap' => true,
      ),
    ),
    'logo' => 
    array (
      'props' => 
      array (
        'layoutType' => 'text',
      ),
    ),
    'header-menu' => 
    array (
      'props' => 
      array (
        'hoverEffect' => 
        array (
          'type' => 'none',
          'group' => 
          array (
            'border' => 
            array (
              'transition' => 'effect-borders-grow grow-from-left',
            ),
          ),
        ),
      ),
    ),
    'hero' => 
    array (
      'props' => 
      array (
        'heroSection' => 
        array (
          'layout' => 'textOnly',
        ),
      ),
      'style' => 
      array (
        'descendants' => 
        array (
          'outer' => 
          array (
            'padding' => 
            array (
              'top' => 
              array (
                'value' => 60,
              ),
              'bottom' => 
              array (
                'value' => 0,
              ),
            ),
            'background' => 
            array (
              'overlay' => 
              array (
                'enabled' => true,
                'gradient_opacity' => 0,
              ),
            ),
          ),
        ),
      ),
      'full_height' => false,
      'hero_column_width' => 80,
    ),
    'buttons' =>
    array (
      'value' => 
      array (
        0 => 
        array (
          'label' => 'Start Your Journey',
        ),
        1 => 
        array (
          'label' => 'Contact us',
        ),
      ),
    ),
  ),
  'header' => 
  array (
    'navigation' => 
    array (
      'props' => 
      array (
        'showTopBar' => false,
        'sticky' => true,
        'overlap' => true,
      ),
    ),
    'header-menu' => 
    array (
      'props' => 
      array (
        'hoverEffect' => 
        array (
          'type' => 'none',
          'group' => 
          array (
            'border' => 
            array (
              'transition' => 'effect-borders-grow grow-from-left',
            ),
          ),
        ),
      ),
    ),
    'logo' => 
    array (
      'props' => 
      array (
        'layoutType' => 'text',
      ),
    ),
  ),
);
