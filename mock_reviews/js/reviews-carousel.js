(function (Drupal, drupalSettings) {
  'use strict';

  Drupal.behaviors.reviewsCarousel = {
    attach: function (context, settings) {
      const reviewCarousels = context.querySelectorAll('.mock-reviews-carousel:not(.js-processed)');
      
      reviewCarousels.forEach((carousel) => {
        carousel.classList.add('js-processed');
        
        const config = {
          loop: true,
          slidesPerView: 1,
          spaceBetween: 20,
          centeredSlides: true,
          autoplay: drupalSettings.mockReviews.autoplay ? {
            delay: drupalSettings.mockReviews.autoplaySpeed,
            disableOnInteraction: false,
          } : false,
          pagination: {
            el: '.swiper-pagination',
            clickable: true,
          },
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          breakpoints: {
            640: {
              slidesPerView: 1,
            },
            768: {
              slidesPerView: 2,
            },
            1024: {
              slidesPerView: 3,
            },
          }
        };

        new Swiper(carousel, config);
      });
    }
  };
})(Drupal, drupalSettings);