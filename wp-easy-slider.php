<?php

function ba_slider_assets() {

    /* ================= CSS ================= */
    wp_register_style('ba-slider-style', false);
    wp_enqueue_style('ba-slider-style');

    wp_add_inline_style('ba-slider-style', '
    .ba-slider-wrapper { width:100%; position:relative; overflow:hidden; }
    .ba-slide { display:none; animation:fade .5s ease; margin-top: -20px !important; }
    @keyframes fade { from{opacity:.4} to{opacity:1} }
    .ba-arrow {
        position:absolute;
        top:50%;
        transform:translateY(-50%);
        background:rgba(0,0,0,0.5);
        color:#fff;
        padding:10px 20px;
        cursor:pointer;
        z-index:30;
    }
    .ba-prev { left:10px; }
    .ba-next { right:10px; }

    .image-comparison { width:100%; position:relative; }
    .image-comparison__slider-wrapper { position:relative; width:100%; }
    .image-comparison__range {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100% !important;
        margin: 0;
        padding: 0;
        background-color: transparent;
        border: none;
        -webkit-appearance: none;
        appearance: none;
        outline: none;
        cursor: ew-resize;
        z-index: 20;
    }
    .image-comparison__range:hover ~ .image-comparison__slider .image-comparison__thumb {
        transform: scale(1.2);
    }
    .image-comparison__range:active ~ .image-comparison__slider .image-comparison__thumb,
    .image-comparison__range:focus ~ .image-comparison__slider .image-comparison__thumb,
    .image-comparison__range--active ~ .image-comparison__slider .image-comparison__thumb {
        transform: scale(0.8);
        background-color: rgba(0, 97, 127, 0.5);
    }
    .image-comparison__image-wrapper {
        position: relative;
        width: 100%;
        padding-top: 56%; /* aspect ratio */
        overflow: hidden;
    }
    .image-comparison__image-wrapper--overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 2;
        padding-top: 0; /* override */
        clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);
        -webkit-clip-path: polygon(0 0, 50% 0, 50% 100%, 0 100%);
    }
    .image-comparison__image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    .image-comparison__slider {
        position: absolute;
        top: 0;
        left: 50%;
        width: 1px;
        height: 100%;
        background-color: #ffffff42;
        transition: background-color 0.3s ease-in-out;
        z-index: 10;
        pointer-events: none;
    }
    .image-comparison__range--active ~ .image-comparison__slider {
        background-color: rgba(255, 255, 255, 0);
    }
    .image-comparison__thumb {
        position: absolute;
        top: calc(50% - 20px);
        left: calc(50% - 20px);
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #000;
        color: #fff;
        border-radius: 50%;
        box-shadow: 0 0 22px 0 rgba(0, 0, 0, 0.5);
        transform-origin: center;
        transition: transform 0.3s ease-in-out, background-color 0.3s ease-in-out;
        pointer-events: none;
    }
     .image-comparison__thumb svg{
        margin-top: 1px;
     }
    .image-comparison__range::-webkit-slider-runnable-track { width: 40px; height: 40px; opacity: 0; }
    .image-comparison__range::-moz-range-thumb { width: 40px; height: 40px; opacity: 0; }
    .image-comparison__range::-webkit-slider-thumb { width: 40px; height: 40px; opacity: 0; }
    .image-comparison__range::-ms-fill-lower { background-color: transparent; }
    .image-comparison__range::-ms-track {
        position: relative; top: 0; left: 0; width: 100%; height: 100%;
        border: none; margin: 0; padding: 0; background-color: transparent;
        color: transparent; outline: none; cursor: col-resize;
    }
    .image-comparison__range::-ms-thumb { width: 0.5%; height: 100%; opacity: 0; }
    .image-comparison__range::-ms-tooltip { display: none; }
    ');

    /* ================= JS ================= */
    wp_register_script('ba-slider-js', false, [], false, true);
    wp_enqueue_script('ba-slider-js');

    wp_add_inline_script('ba-slider-js', "
    document.addEventListener('DOMContentLoaded', function(){

        document.querySelectorAll('.ba-slider-wrapper').forEach(wrapper => {

            let index = 0;
            const slides = wrapper.querySelectorAll('.ba-slide');

            function showSlide(i){
                slides.forEach(s => s.style.display='none');
                slides[i].style.display='block';
            }

            wrapper.querySelector('.ba-next').onclick = () => {
                index = (index+1) % slides.length;
                showSlide(index);
            };

            wrapper.querySelector('.ba-prev').onclick = () => {
                index = (index-1+slides.length)%slides.length;
                showSlide(index);
            };

            showSlide(index);

            /* ===== BEFORE-AFTER CODEPEN FIXED ===== */
            wrapper.querySelectorAll('[data-component=\"image-comparison-slider\"]').forEach(element => {
                const sliderRange = element.querySelector('[data-image-comparison-range]');
                const slider = element.querySelector('[data-image-comparison-slider]');
                const thumb = element.querySelector('[data-image-comparison-thumb]');
                const imageWrapperOverlay = element.querySelector('[data-image-comparison-overlay]');

                if (!sliderRange || !slider || !thumb || !imageWrapperOverlay) return;

                function setSliderstate(e) {
                    if (e.type === 'input') {
                        sliderRange.classList.add('image-comparison__range--active');
                        return;
                    }
                    sliderRange.classList.remove('image-comparison__range--active');
                    element.removeEventListener('mousemove', moveSliderThumb);
                }

                function moveSliderThumb(e) {
                    const rect = element.getBoundingClientRect();
                    let position = (e.clientY - rect.top) - 20;

                    if (position <= -20) { position = -20; }
                    if (position >= rect.height - 20) { position = rect.height - 20; }

                    thumb.style.top = position + 'px';
                }

                function moveSliderRange(e) {
                    const value = e.target.value;
                    slider.style.left = value + '%';
                    imageWrapperOverlay.style.clipPath = 'polygon(0 0, ' + value + '% 0, ' + value + '% 100%, 0 100%)';
                    imageWrapperOverlay.style.webkitClipPath = 'polygon(0 0, ' + value + '% 0, ' + value + '% 100%, 0 100%)';

                    element.addEventListener('mousemove', moveSliderThumb);
                    setSliderstate(e);
                }

                if ('ontouchstart' in window === false) {
                    sliderRange.addEventListener('mouseup', e => setSliderstate(e));
                    sliderRange.addEventListener('mousedown', moveSliderThumb);
                }

                sliderRange.addEventListener('input', e => moveSliderRange(e));
                sliderRange.addEventListener('change', e => moveSliderRange(e));
            });
        });
    });
    ");
}
add_action('wp_enqueue_scripts','ba_slider_assets');

function ba_slider_shortcode() {
ob_start(); 
$svg_icon = '<svg class="image-comparison__thumb-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="10" viewBox="0 0 18 10" fill="currentColor">
    <path class="image-comparison__thumb-icon--left" d="M12.121 4.703V.488c0-.302.384-.454.609-.24l4.42 4.214a.33.33 0 0 1 0 .481l-4.42 4.214c-.225.215-.609.063-.609-.24V4.703z"></path>
    <path class="image-comparison__thumb-icon--right" d="M5.879 4.703V.488c0-.302-.384-.454-.609-.24L.85 4.462a.33.33 0 0 0 0 .481l4.42 4.214c.225.215.609.063.609-.24V4.703z"></path>
</svg>';
?>
<div class="ba-slider-wrapper">
    <div class="ba-slide">
        <div class="image-comparison" data-component="image-comparison-slider">
            <div class="image-comparison__slider-wrapper">
                <input type="range" min="0" max="100" value="50" class="image-comparison__range" data-image-comparison-range>
                <div class="image-comparison__image-wrapper image-comparison__image-wrapper--overlay" data-image-comparison-overlay>
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2020/07/1000x600-3.jpg" class="image-comparison__image">
                </div>
                <div class="image-comparison__slider" data-image-comparison-slider>
                    <span class="image-comparison__thumb" data-image-comparison-thumb>
                        <?php echo $svg_icon; ?>
                    </span>
                </div>
                <div class="image-comparison__image-wrapper">
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2020/06/sideview.jpg" class="image-comparison__image">
                </div>
            </div>
        </div>
    </div>
    <div class="ba-slide">
        <div class="image-comparison" data-component="image-comparison-slider">
            <div class="image-comparison__slider-wrapper">
                <input type="range" min="0" max="100" value="50" class="image-comparison__range" data-image-comparison-range>
                <div class="image-comparison__image-wrapper image-comparison__image-wrapper--overlay" data-image-comparison-overlay>
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2022/09/RYSE-Creative-Village-Front-viewweb-scaled-e1593168420700.webp" class="image-comparison__image">
                </div>
                <div class="image-comparison__slider" data-image-comparison-slider>
                    <span class="image-comparison__thumb" data-image-comparison-thumb>
                        <?php echo $svg_icon; ?>
                    </span>
                </div>
                <div class="image-comparison__image-wrapper">
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2022/09/RYSE-Creative-Village-Front-view-scaled-1.webp" class="image-comparison__image">
                </div>
            </div>
        </div>
    </div>
    <div class="ba-slide">
        <div class="image-comparison" data-component="image-comparison-slider">
            <div class="image-comparison__slider-wrapper">
                <input type="range" min="0" max="100" value="50" class="image-comparison__range" data-image-comparison-range>
                <div class="image-comparison__image-wrapper image-comparison__image-wrapper--overlay" data-image-comparison-overlay>
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2022/09/Image-1-scaled-1.webp" class="image-comparison__image">
                </div>
                <div class="image-comparison__slider" data-image-comparison-slider>
                    <span class="image-comparison__thumb" data-image-comparison-thumb>
                        <?php echo $svg_icon; ?>
                    </span>
                </div>
                <div class="image-comparison__image-wrapper">
                    <img src="https://rysecreativevillage.com/wp-content/uploads/2026/04/Image-5-scaled.jpg" class="image-comparison__image">
                </div>
            </div>
        </div>
    </div>
    <div class="ba-arrow ba-prev">&#10094;</div>
    <div class="ba-arrow ba-next">&#10095;</div>
</div>
<?php
return ob_get_clean();
}
add_shortcode('before_after_slider','ba_slider_shortcode');


