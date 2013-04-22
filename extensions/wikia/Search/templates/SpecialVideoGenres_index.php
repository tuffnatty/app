<ul>
<?php foreach ( $facets as $facet => $count ): ?>
    <li><a href="?genre=<?=$facet?>"><?=$facet?></a> ( <?=$count?> )</li>
<?php endforeach; ?>
</ul>