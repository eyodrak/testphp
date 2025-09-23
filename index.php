<?php
// youtube_player.php
// PHP 5 compatible single-page script to show 7 YouTube clips as thumbnails and play inline with API data + animations

$videos = array(
    'pENCMMXZR34',
    'bw_KPw4TuRE',
    '2IHWltMHSTc',
    'alRJ5j3-WhI',
    '05yL4RzE_TE',
    'BP2QU7H8D1k',
    'zlz9xEOK4W8'
);
$first = $videos[0];

// YouTube API key (replace with your own valid key)
$apiKey = 'AIzaSyB66gjpf8oEsef0Nzn25JzIHs-46YjzMp0';

// Function to fetch video details
function getVideoDetails($id, $apiKey){
    $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id={$id}&key={$apiKey}";
    $json = @file_get_contents($url);
    if($json === FALSE){
        return array('title'=>"Video $id");
    }
    $data = json_decode($json, true);
    if(isset($data['items'][0])){
        $title = $data['items'][0]['snippet']['title'];
        return array('title'=>$title);
    }
    return array('title'=>"Video $id");
}

$details = array();
foreach($videos as $vid){
    $details[$vid] = getVideoDetails($vid, $apiKey);
}
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>YodRak Studio</title>
  <style>
    body{font-family: 'Segoe UI', Roboto, Arial, sans-serif; background:#000; color:#eee; margin:0; padding:30px}
    .card{max-width:1200px;margin:0 auto;background:rgba(20,20,20,0.9);box-shadow:0 10px 30px rgba(0,0,0,0.7);border-radius:14px;padding:24px;animation:fadeIn 1s ease-in-out}
    .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    .brand{display:flex;align-items:center;gap:12px}
    .profile-pic{width:40px;height:40px;border-radius:50%;overflow:hidden;border:2px solid #ff5555;flex-shrink:0}
    .profile-pic img{width:100%;height:100%;object-fit:cover}
    .title{font-size:24px;font-weight:700;color:#ff5555;animation:slideDown 0.8s ease-out}
    .title a{text-decoration:none;color:#ff5555}
    .layout{display:flex;gap:20px;flex-wrap:wrap}
    .player{flex:1 1 640px;min-width:300px;position:relative}
    .player iframe{width:100%;height:400px;border-radius:10px;border:0;animation:fadeIn 0.6s ease-in-out}
    .meta{margin-top:12px;display:flex;align-items:center;justify-content:flex-start;color:#aaa;font-size:14px;animation:fadeIn 1s ease-in-out}
    .thumbnails{width:360px;flex:0 0 360px;display:flex;flex-direction:column;gap:12px;animation:slideUp 1s ease-out}
    .thumb{display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;cursor:pointer;transition:transform .3s,box-shadow .3s,background .3s;background:#111;color:#eee}
    .thumb:hover{transform:translateY(-6px) scale(1.02);box-shadow:0 12px 24px rgba(0,0,0,0.9);background:#1a1a1a}
    .thumb img{width:120px;height:67px;object-fit:cover;border-radius:6px;transition:transform .3s}
    .thumb:hover img{transform:scale(1.05)}
    .thumb .info{font-size:13px}
    .thumb .titleText{font-weight:600;font-size:14px;margin-bottom:4px;color:#fff}
    .thumb.active{box-shadow:0 10px 30px rgba(0,0,0,0.9);background:#222}
    .open-youtube{font-size:13px;text-decoration:none;padding:8px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);color:#eee;transition:background .3s}
    .open-youtube:hover{background:#333}
    @media(max-width:900px){.layout{flex-direction:column}.thumbnails{width:100%;flex-direction:row;overflow-x:auto;padding-bottom:6px}.thumb{min-width:240px}}

    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    @keyframes slideDown{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
    @keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <div class="brand">
        <div class="profile-pic"><img src="yodrak.jpg" alt="profile"></div>
        <div class="title"><a href="https://www.youtube.com/@yodrak" target="_blank">YodRak Studio</a></div>
      </div>
      <div><a id="openYT" class="open-youtube" href="https://www.youtube.com/watch?v=<?php echo $first; ?>" target="_blank">Open on YouTube</a></div>
    </div>

    <div class="layout">
      <div class="player">
        <iframe id="player" src="https://www.youtube.com/embed/<?php echo $first; ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <div class="meta">
          <div id="titleText">Now playing: <?php echo htmlspecialchars($details[$first]['title']); ?></div>
        </div>
      </div>

      <div class="thumbnails" id="thumbs">
        <?php foreach($videos as $vid):
            $thumb = "https://img.youtube.com/vi/".$vid."/hqdefault.jpg";
            $title = htmlspecialchars($details[$vid]['title']);
        ?>
          <div class="thumb" data-id="<?php echo $vid; ?>" data-title="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
            <img src="<?php echo $thumb; ?>" alt="thumb">
            <div class="info">
              <div class="titleText"><?php echo $title; ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script>
    (function(){
      var thumbs = document.getElementById('thumbs');
      var player = document.getElementById('player');
      var openLink = document.getElementById('openYT');
      var titleText = document.getElementById('titleText');
      function setActive(el){
        var nodes = thumbs.querySelectorAll('.thumb');
        for(var i=0;i<nodes.length;i++){nodes[i].className = 'thumb'}
        el.className = 'thumb active';
      }
      var first = thumbs.querySelector('.thumb'); if(first) setActive(first);

      thumbs.addEventListener('click', function(e){
        var t = e.target;
        while(t && !t.classList.contains('thumb')) t = t.parentNode;
        if(!t) return;
        var id = t.getAttribute('data-id');
        var title = t.getAttribute('data-title');
        if(!id) return;
        player.classList.remove('fadeIn');
        void player.offsetWidth;
        player.classList.add('fadeIn');
        player.src = 'https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0';
        openLink.href = 'https://www.youtube.com/watch?v=' + id;
        titleText.textContent = title;
        setActive(t);
      });
    })();
  </script>
</body>
</html>
