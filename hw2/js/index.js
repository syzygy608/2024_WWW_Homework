var recording = false;
var recordingArray = [];

function playSound(key, playing_recording = false)
{
    if(key >= "A" && key <= "Z")
        key = key.toLowerCase();
    var button = document.getElementById("key_" + key);
    if(key == ";")
        button = document.getElementById("key_semi");
    if(button)
    {
        if(recording && !playing_recording)
            recordingArray.push(key);
        button.classList.add("pressed");
        var volume = document.getElementById("range").value;
        var audio = new Audio('tunes/' + key + '.wav');
        audio.volume = volume / 100;
        audio.play();
        setTimeout(function()
        {
            button.classList.remove("pressed");
        }, 100);
    }
}

function startRecording(value)
{
    recording = value;
}

function clearRecord()
{
    recording = false;
    recordingArray = [];
    var checkbox = document.getElementById("record");
    checkbox.checked = false;
    alert("已清空!");
}

function playRecord()
{
    for(var i = 0; i < recordingArray.length; i++)
    {
        setTimeout(playSound, i * 500, recordingArray[i], true);
    }
}

document.addEventListener('keydown', function(event)
{
    var key = event.key;
    playSound(key);
});