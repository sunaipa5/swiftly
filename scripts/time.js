function updateTime() {
    const currentTime = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    const formattedDateTime = new Intl.DateTimeFormat('tr-TR', options).format(currentTime);
    document.getElementById('info').innerHTML = `<p>${formattedDateTime} - ${currentTime.getDate()}/${currentTime.getMonth() + 1}/${currentTime.getFullYear()}</p>`;
}

updateTime();
setInterval(updateTime, 1000);
