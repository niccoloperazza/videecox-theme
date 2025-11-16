<?php

add_action('wp_footer', 'vid_smooth_animations');
function vid_smooth_animations() {
?>
<style>
/* Fade in per article */
article {
  opacity: 0;
  animation: fadeInArticle 0.8s ease-out 0.2s forwards;
}

@keyframes fadeInArticle {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Fade in elementi contenuto */
.fade-in-element {
  opacity: 0;
  transform: translateY(30px);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.fade-in-element.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Easing menu */
.main-navigation a,
.ast-nav-menu a {
  transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-navigation a:hover,
.ast-nav-menu a:hover {
  transform: translateY(-2px);
}

/* Easing link generali */
a {
  transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

a:hover {
  opacity: 0.85;
}

/* Easing link nel contenuto */
.entry-content a {
  transition: color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Seleziona elementi da animare
  const elements = document.querySelectorAll('.entry-content h2, .entry-content p, .entry-content ul, .entry-content h3');

  elements.forEach(el => el.classList.add('fade-in-element'));

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  elements.forEach(el => observer.observe(el));
});
</script>
<?php
}
