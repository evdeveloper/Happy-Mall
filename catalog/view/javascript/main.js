document.addEventListener("DOMContentLoaded", () => {

  const BODY = document.querySelector('body');
  const HEADER = document.querySelector('.header');
  const MOBILE_MENU = document.querySelector('.mobileMenu');
  const CATALOG_MENU = document.querySelector('.catalogMenu');

  let classesArray = ['.menu-open, .menu-open > .sandwich', '.catalog-open, .catalog-open > .sandwich', '.mobileMenu', '.catalogMenu','.searchPopup', '.popup'];

  let headerHeight = HEADER.offsetHeight;

  document.addEventListener('click', e => {
    if(e.target.closest('.card__favorite')) {
      let parent = e.target.closest('.card__favorite');
      let use = parent.querySelector('use');
      let url = use.getAttributeNS('http://www.w3.org/1999/xlink', 'href').split('#')[0];
      parent.classList.toggle('active');
      if(parent.classList.contains('active')) {
        use.setAttributeNS('http://www.w3.org/1999/xlink', 'href', `${url}#like-fill`);
      } else {
        use.setAttributeNS('http://www.w3.org/1999/xlink', 'href', `${url}#like-outline`);
      }
    }
  });
  document.querySelectorAll('input.mask').forEach(input => IMask(input, { mask: '+{7} (000) 000-00-00' }));

  window.addEventListener('scroll', e => {
    if(headerHeight < window.scrollY) {
      HEADER.classList.add('fixed');
      HEADER.closest('.wrapper').style.paddingTop = `${headerHeight}px`;
    } else {
      HEADER.classList.remove('fixed');
      HEADER.closest('.wrapper').style.paddingTop = 0;
    }
  });

  $('.catalogMenu__tab:first-child').addClass('active');
  $('.catalogMenu__col:first-child').show();
  $(document).on('click', '.catalogMenu__tab', function() {
    $(this).addClass('active').siblings().removeClass('active');
    $(this).closest('.catalogMenu').find('.catalogMenu__col').eq($(this).index()).fadeIn().siblings().hide();
  });
  
  $(document).on('click', '.catalog-open', function(){
    $(this).toggleClass('active', $(this).hasClass('active') ? removeOverlay() : addOverlay());
    $(this).find('.sandwich').toggleClass('active');
    $(CATALOG_MENU).toggleClass('active');
  });
  $(document).on('click', '.searchPopup__close', function(){
    $(this).closest('.searchPopup').find('.searchPopup__input').val('');
  });
  $(document).on('click', '.searchTags__item', function(){
    $(this).closest('.searchPopup').find('.searchPopup__input').val($(this).text()).focus();
  });
  $(document).on('click', '.search-open', function(){
    $(BODY).find('.searchPopup').addClass('active');
    addOverlay();
  });
  $(document).on('click', '.menu-open', function(){
    $(this).toggleClass('active', $(this).hasClass('active') ? removeOverlay() : addOverlay());
    $(this).find('.sandwich').toggleClass('active');
    $(MOBILE_MENU).toggleClass('active');
  });
  $(document).on('click', '.overlay', function(){
    removeOverlay();
    $('.searchPopup__input').val('');
    closeModal(BODY.querySelector('.popup').getAttribute('data-popup-target'));
    classesArray.forEach(classes => $(classes).removeClass('active'));
  });

  if (window.matchMedia("(max-width: 1100px)").matches) {
    $(document).on('click', '.mobileMenu__item', function(){
      $(this).toggleClass('active').find('.mobileSubmenu').slideToggle();
      $(this).siblings().removeClass('active').find('.mobileSubmenu').slideUp();
    });
  }
  if (window.matchMedia("(max-width: 481px)").matches) {
    $(document).on('click', '.nav-footer__top', function(){
      $(this).toggleClass('active').next().slideToggle();
      $(this).parent().siblings().find('.nav-footer__top').removeClass('active').next().slideUp();
    });
  }

  function openModal(id) {
    document.querySelector(`[data-popup-target="${id}"]`).classList.add('active');
    if(BODY.querySelector('.overlay')) { return false; }
    addOverlay(2000);
  }
  function closeModal(id) {
    document.querySelector(`[data-popup-target="${id}"]`).classList.remove('active');
    if(BODY.querySelector('.overlay')) { removeOverlay(); }
  }

  $(document).on('click', '[data-popup-open]', function(e){
    e.preventDefault();
    document.querySelector('.popup.active')?.classList.remove('active');
    $('.menu-open, .menu-open > .sandwich, .mobileMenu').removeClass('active');
    if(document.querySelector('.overlay')) { document.querySelector('.overlay').style.zIndex = 2000; }
    openModal(e.target.closest('[data-popup-open]').getAttribute('data-popup-open'));
  });

  $(document).on('click', '[data-popup-close]', function(e){
    closeModal(e.target.closest('.popup').getAttribute('data-popup-target'));
  });

  function addHiddenBody() {
    BODY.classList.add('overflow-hidden');
  }
  function removeHiddenBody() {
    BODY.classList.remove('overflow-hidden');
  }
  function addOverlay(zindex) {
    let div = document.createElement('div');
    div.classList.add('overlay');
    BODY.appendChild(div);
    BODY.querySelector('.overlay').style.zIndex = `${zindex}`;
    addHiddenBody();
  }
  function removeOverlay() {
    BODY.querySelector('.overlay').remove();
    removeHiddenBody();
  }


  const swiperBanner = new Swiper('.banner__swiper', {
    autoHeight: true,
    keyboard: true,
    grabCursor: true,
    autoplay: {
      delay: 5000,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    },
  });

  const swiperReviews = new Swiper('.swiper-reviews', {
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-arrows__prev',
      prevEl: '.swiper-arrows__next'
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 10
      },
      480: {
        slidesPerView: 1,
        spaceBetween: 10
      },
      768: {
        slidesPerView: 2
      },
      960: {
        slidesPerView: 3,
        spaceBetween: 20
      }
    }
  });
  
  const swiperCards = new Swiper('.swiper-cards-gallery', {
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.swiper-arrows__prev',
      prevEl: '.swiper-arrows__next'
    },
    breakpoints: {
      320: {
        slidesPerView: 2,
        spaceBetween: 10
      },
      600: {
        slidesPerView: 3,
        spaceBetween: 10
      },
      768: {
        slidesPerView: 3
      },
      960: {
        slidesPerView: 4,
        spaceBetween: 20
      }
    }
  });
  const swiperCardGallery = new Swiper('.swiper-card-gallery', {
    keyboard: true,
    grabCursor: true,
    pagination: {
      el: '.swiper-pagination',
      clickable: true
    }
  });
  
});