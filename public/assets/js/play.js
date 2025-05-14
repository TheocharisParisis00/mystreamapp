function playSong(videoId) {
  document.getElementById("videoPlayer").src =
    "https://www.youtube.com/embed/" + videoId + "?autoplay=1";
}
