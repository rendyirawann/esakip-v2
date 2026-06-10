<style>
    .draggable-card {
        position: absolute;
        width: 250px;
        height: auto;
        background: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 16px;
        cursor: move;
        z-index: 1000;
        top: 80px;
        /* Default position */
        right: 40px;
        /* Default position */
        transition: box-shadow 0.2s ease-in-out;
    }

    .draggable-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .draggable-card-header {
        font-weight: bold;
        text-align: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
        margin-bottom: 8px;
    }

    .draggable-card-body {
        font-size: 14px;
        color: #555;
    }
</style>
<?php

/** @var \yii\web\View $this */
/** @var string $content */

// use frontend\assets\AppAsset;
use frontend\models\User;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
use mdm\admin\components\MenuHelper;

// AppAsset::register($this);
?>

<div class="draggable-card" id="card-1">
    <div class="card-header draggable-card-header">
        <h5>Draggable Card</h5>
    </div>
    <div class="card-body draggable-card-body">
        <p>Content of the draggable card.</p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const draggableCard = document.getElementById("card-1");

        let isDragging = false;
        let offsetX, offsetY;

        draggableCard.addEventListener("mousedown", (e) => {
            isDragging = true;
            offsetX = e.clientX - draggableCard.getBoundingClientRect().left;
            offsetY = e.clientY - draggableCard.getBoundingClientRect().top;
            draggableCard.style.cursor = "grabbing";
        });

        document.addEventListener("mousemove", (e) => {
            if (isDragging) {
                draggableCard.style.left = `${e.clientX - offsetX}px`;
                draggableCard.style.top = `${e.clientY - offsetY}px`;
            }
        });

        document.addEventListener("mouseup", () => {
            isDragging = false;
            draggableCard.style.cursor = "move";
        });
    });
</script>