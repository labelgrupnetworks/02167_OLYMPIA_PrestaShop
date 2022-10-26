<?php
namespace Elementor;

if ( ! defined( 'ELEMENTOR_ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Lottie extends Widget_Base {

	public function get_id() {
		return 'lottie';
	}

	public function get_title() {
		return \IqitElementorWpHelper::__( 'Lottie animation', 'elementor' );
	}

	public function get_icon() {
		return 'animation';
	}

	protected function _register_controls() {
		$this->add_control(
			'section_lottie',
			[
				'label' => \IqitElementorWpHelper::__( 'Lottie animation', 'elementor' ),
				'type' => Controls_Manager::SECTION,
			]
		);

        $this->add_control(
            'animation_url',
            [
                'label' => \IqitElementorWpHelper::__( 'Link to json animation file', 'elementor' ),
                'type' => Controls_Manager::TEXT,
                'section' => 'section_lottie',
                'placeholder' => \IqitElementorWpHelper::__( 'Enter your Lottie animation link', 'elementor' ),
                'default' => 'https://assets2.lottiefiles.com/private_files/lf30_AYF2Aw.json',
                'description' => 'You can create own lottie animations, or check <a href="https://lottiefiles.com/" target="_blank">Lottiefiles.com library</a>',
                'label_block' => true,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => \IqitElementorWpHelper::__( 'Alignment', 'elementor' ),
                'type' => Controls_Manager::CHOOSE,
                'section' => 'section_lottie',
                'options' => [
                    'flex-start'    => [
                        'title' => \IqitElementorWpHelper::__( 'Left', 'elementor' ),
                        'icon' => 'align-left',
                    ],
                    'center' => [
                        'title' => \IqitElementorWpHelper::__( 'Center', 'elementor' ),
                        'icon' => 'align-center',
                    ],
                    'flex-end' => [
                        'title' => \IqitElementorWpHelper::__( 'Right', 'elementor' ),
                        'icon' => 'align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-lottie' => 'display: flex; justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => \IqitElementorWpHelper::__( 'Width', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'section' => 'section_lottie',
                'size_units' => [ 'px', '%'],
                'range' => [
                    'px' => [
                        'max' => 2000,
                    ],
                    '%' => [
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 150,
                ],
                'selectors' => [
                    '{{WRAPPER}} .lottie-animation' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'height',
            [
                'label' => \IqitElementorWpHelper::__( 'Height', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'section' => 'section_lottie',
                'size_units' => [ 'px', '%'],
                'range' => [
                    'px' => [
                        'max' => 2000,
                    ],
                    '%' => [
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => 150,
                ],
                'selectors' => [
                    '{{WRAPPER}} .lottie-animation' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'rotate',
            [
                'label' => \IqitElementorWpHelper::__( 'Rotate', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'section' => 'section_lottie',
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -180,
                        'max' => 180,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lottie-animation' => '
                    -webkit-transform: rotate( {{SIZE}}deg); 
                    -ms-transform: rotate({{SIZE}}deg); 
                    transform: rotate( {{SIZE}}deg);',
                ],
            ]
        );


        $this->add_control(
            'background',
            [
                'label' => \IqitElementorWpHelper::__( 'Background', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'section' => 'section_lottie',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lottie-animation' => 'background-color: {{VALUE}};',
                ],
            ]
        );


        $this->add_control(
            'link',
            [
                'label' => \IqitElementorWpHelper::__( 'Link to', 'elementor' ),
                'type' => Controls_Manager::URL,
                'placeholder' => \IqitElementorWpHelper::__( 'http://your-link.com', 'elementor' ),
                'section' => 'section_lottie',
            ]
        );


        $this->add_control(
            'section_lottie_options',
            [
                'label' => \IqitElementorWpHelper::__( 'Lottie animation options', 'elementor' ),
                'type' => Controls_Manager::SECTION,
            ]
        );


        $this->add_control(
            'speed',
            [
                'label' => \IqitElementorWpHelper::__( 'Speed', 'elementor' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'default' => 1,
                'step' => 0.1,
                'section' => 'section_lottie_options',
            ]
        );

        $this->add_control(
            'play_on',
            [
                'label' => \IqitElementorWpHelper::__( 'Play on', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'autoplay',
                'options' => [
                    'autoplay' => \IqitElementorWpHelper::__( 'Autoplay', 'elementor' ),
                    'hover' => \IqitElementorWpHelper::__( 'Hover', 'elementor' ),
                    'scroll' => \IqitElementorWpHelper::__( 'Scroll', 'elementor' ),
                ],
                'section' => 'section_lottie_options',
            ]
        );


        $this->add_control(
            'container',
            [
                'label' => \IqitElementorWpHelper::__( 'Offset container', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'inline',
                'options' => [
                    'inline' => \IqitElementorWpHelper::__( 'inline', 'elementor' ),
                    'body' => \IqitElementorWpHelper::__( 'body', 'elementor' ),
                ],
                'section' => 'section_lottie_options',
                'condition' => [
                    'play_on' => 'scroll',
                ],
            ]
        );


        $this->add_control(
            'lottie_offset',
            [
                'label' => \IqitElementorWpHelper::__( 'Offset [%]', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'section' => 'section_lottie_options',
                'size_units' => [ 'px'],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => -100,
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'condition' => [
                    'play_on' => 'scroll',
                ],
            ]
        );



        $this->add_control(
            'loop',
            [
                'label' => \IqitElementorWpHelper::__( 'Loop', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'no' => \IqitElementorWpHelper::__( 'No', 'elementor' ),
                    'yes' => \IqitElementorWpHelper::__( 'Yes', 'elementor' ),
                ],
                'section' => 'section_lottie_options',
            ]
        );


        $this->add_control(
            'mode',
            [
                'label' => \IqitElementorWpHelper::__( 'Play mode', 'elementor' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'normal',
                'options' => [
                    'normal' => \IqitElementorWpHelper::__( 'normal', 'elementor' ),
                    'bounce' => \IqitElementorWpHelper::__( 'bounce', 'elementor' ),
                ],
                'section' => 'section_lottie_options',
            ]
        );

	}

	protected function render( $instance = [] ) {

        if (!PluginElementor::instance()->editor->is_edit_mode()) { ?>
            <script>
                document.addEventListener("DOMContentLoaded", function(event) {
                    loadElementorLottiePlayer();
                });
            </script>
            <?php }

        $params = '';
        $lottyFile = '';
        if ($instance['loop'] == 'yes') {
            $params .= 'loop ';
        }
        if ($instance['play_on'] == 'autoplay') {
            $params .= 'autoplay ';
        }
        if ($instance['play_on'] == 'hover') {
            $params .= 'hover ';
        }
        if ($instance['mode'] == 'bounce') {
            $params .= 'mode="bounce" ';
        }
        if ($instance['animation_url']) {
            $lottyFile = '<lottie-player id="elementor-video-modal-'.(isset($instance['id_widget_instance']) ? $instance['id_widget_instance'] : '') .'" data-offset="'.$instance['lottie_offset']['size'].'" data-play="'.$instance['play_on'].'" data-container="'.$instance['container'].'" class="lottie-animation" src="'.$instance['animation_url'].'"  background="transparent"  speed="'.$instance['speed'].'"  '. $params .'></lottie-player>';
        }


        if ( ! empty( $instance['link']['url'] ) ) {
            $target = '';
            if ( ! empty( $instance['link']['is_external'] ) ) {
                $target = ' target="_blank" rel="noopener noreferrer"';
            }
            $lottyFile = sprintf( '<a href="%s"%s>%s</a>', $instance['link']['url'], $target, $lottyFile );
        }

        echo  $lottyFile;
    }

	protected function content_template() {
	}
}
