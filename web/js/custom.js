var conn = new WebSocket('ws://rps-game:8080');
var gesture;
var timer;
var game=false;

conn.onopen = function(e) {
    console.info("Connection established succesfully");
};

conn.onmessage = function(e) {
    //console.log(e.data);
    //if opponent reload a page
    if(e.data=='start' && game){
        stopGame();
        startGame();
    }
    //first start a game
    if(e.data=='start' && !game){
        startGame();
    }
    //get message with a winning gesture
    if(['rock', 'paper', 'scissors'].indexOf(e.data) != -1){
        if(gesture==e.data) $('.time').text('You win');
        else $('.time').text('You lose');
        $('#play').show();
        stopGame();
    }
    //draw message
    if(e.data=='draw'){
        $('.time').text('Draw');
        stopGame();
    }
    //opponent's timeout
    if(e.data=='timeOut'){
        if(gesture) $('.time').text('Opponent didn\'n choose a gesture');
        stopGame();
    }
};

$('body').on('click', '#play', function () {
    $(this).attr("value","Waiting for an opponent...");
    $(this).attr("disabled", "disabled");
    conn.send('play');
});

$('body').on('click', 'img', function () {
    var c = $(this).attr('class');
    gesture = c;
    conn.send(c);
    clearInterval(timer);
    $('.game').empty();
    $('.time').text('You selected: ' + c);
});

function stopGame() {
    $('#play').show();
    $('#play').attr("value","Play again");
    $('#play').removeAttr("disabled");
    $('.game').empty();
    game=false;
    gesture=false;
}

function startGame() {
    game=true;
    $('#play').hide();
    $('.game').empty();
    $('.game').append(
        "<h2> Choose the gesture </h2>"+
        '<img src="/img/rock.png" class="rock">'+
        '<img src="/img/paper.png" class="paper">'+
        '<img src="/img/scissors.png" class="scissors">'
    );

    var x = 10;
    timer = setInterval(function () {
        $('.time').text(x-- + " sec");
        if(x===-1) {
            conn.send('timeOut');
            $('.time').text("Time Out");
            clearInterval(timer);
            stopGame();
        }
    }, 1000);
}