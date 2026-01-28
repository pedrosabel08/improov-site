const videos = [
    "animas/Infinita/1.mp4",
    "animas/Infinita/2.mp4",
    "animas/Infinita/3.mp4",
    "animas/Infinita/4.mp4"
];

let currentVideoIndex = 0;
const videoPlayer = document.getElementById('videoAnima');

// Adicionar evento 'ended' para tocar o próximo vídeo
videoPlayer.addEventListener('ended', () => {
    currentVideoIndex++;
    if (currentVideoIndex < videos.length) {
        videoPlayer.src = videos[currentVideoIndex];
        videoPlayer.play();
    } else {
        currentVideoIndex = 0; // Reinicia a sequência se desejar
        videoPlayer.src = videos[currentVideoIndex];
        videoPlayer.play();
    }
});

// Carregar o primeiro vídeo
videoPlayer.src = videos[currentVideoIndex];


const carouselImages = document.querySelector('.carousel-images');
const images = document.querySelectorAll('.carousel-images img');
const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');

let currentIndex = 0;
const totalImages = images.length;

function showImage(index) {

    carouselImages.style.transform = `translateX(${-index * 100}%)`;
}

nextBtn.addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % totalImages;
    showImage(currentIndex);
});

prevBtn.addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + totalImages) % totalImages;
    showImage(currentIndex);
});

setInterval(() => {
    currentIndex = (currentIndex + 1) % totalImages;
    showImage(currentIndex);
}, 5000); 