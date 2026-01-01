

      <p><img border="0" src="images/mh.gif" width="120" height="60"></p>
      <style>
      .menu-button {
        display: block;
        width: 120px;
        height: 20px;
        margin-bottom: 15px;
        background-size: 120px 20px;
        text-decoration: none;
        border: none;
        cursor: pointer;
      }
      .menu-button.generate {
        background-image: url('images/b1u.gif');
      }
      .menu-button.generate:hover {
        background-image: url('images/b1d.gif');
      }
      .menu-button.view {
        background-image: url('images/b3u.gif');
      }
      .menu-button.view:hover {
        background-image: url('images/b3d.gif');
      }
      .menu-button.play {
        background-image: url('images/b2u.gif');
      }
      .menu-button.play:hover {
        background-image: url('images/b2d.gif');
      }
      .menu-button.config {
        background-image: url('images/b4u.gif');
      }
      .menu-button.config:hover {
        background-image: url('images/b4d.gif');
      }
      </style>
      <p>
	<a href="index.php?action=generate<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="menu-button generate"></a>
	<a href="index.php?action=view<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="menu-button view"></a>
	<a href="index.php?action=play<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="menu-button play"></a>
	<a href="index.php?action=config<?= (isset($_GET["numberinplay"]))?'&numberinplay='.$_GET["numberinplay"]:''; ?>" class="menu-button config"></a>
      </p>
