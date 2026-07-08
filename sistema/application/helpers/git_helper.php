<?php
function get_current_git_commit( $branch='master' ) {
  if ( $hash = file_get_contents( sprintf( '../.git/refs/heads/%s', $branch ) ) ) {
    return substr($hash, 0, 7);
  } else {
    return false;
  }
}
?>