<?php
    session_start();
    require_once '../components/config.php'; 

    if (isset($_SESSION['role']) && ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Staff')) {
        header("Location: staff_page.php");
        exit();
    }

    if (!isset($_SESSION['id'])) {
        $_SESSION['id'] = 1;
        $_SESSION['name'] = "Visitor";
        $_SESSION['role'] = "Visitor";
    }

    require_once '../components/header.php'; 
    require_once '../components/sidebar.php';

    // 1. Fetch all events from the database, sorted by date
    $sql_events = "SELECT * FROM event_list ORDER BY date ASC";
    $result_events = $conn->query($sql_events);
?>

<style>
    /* 1. The Wrapper: Controls the vertical scrolling */
    .gallery-scroll-area {
        max-height: 65vh; 
        overflow-y: auto;
        padding: 20px;
    }

    /* Custom scrollbar */
    .gallery-scroll-area::-webkit-scrollbar { width: 8px; }
    .gallery-scroll-area::-webkit-scrollbar-thumb { background: #b0b0b0; border-radius: 4px; }

    /* 2. The Grid: Controls the 4 columns ONLY */
    .masonry-gallery {
        column-count: 4;
        column-gap: 12px;
    }
    
    .masonry-gallery img {
        width: 100%;
        display: block;
        margin-bottom: 12px;
        border-radius: 6px;
        break-inside: avoid; /* Keeps images from slicing in half */
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="main-content">
    <main class="main-content-wrapper" style="padding: 90px 30px 30px 30px;">

        <!-- // for cardd -->
        <div class="event-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;">
            
            <?php if ($result_events && $result_events->num_rows > 0): ?>
                <?php while($event = $result_events->fetch_assoc()): 
        
                    $image_filename = match((int)$event['id']) {
                        1 => 'culturalevents.jpg',
                        2 => 'techsummit.jpg',
                        3 => 'uniopenhouse.jpg',
                        default => 'default_event.jpg'
                    };
                    
                    $bg_image = '../assets/images/gallery/' . $image_filename;
                    
                    // Calculate the duration dynamically
                    $start_dt = new DateTime($event['start_time']);
                    $end_dt = new DateTime($event['end_time']);
                    $diff = $start_dt->diff($end_dt);
                    $duration_str = $diff->h . ' hours';
                    if ($diff->i > 0) { $duration_str .= ' ' . $diff->i . ' mins'; }

                    // Inject calculated data back into the array so JS can read it easily
                    $event['calculated_duration'] = $duration_str;
                    $event['cover_image_url'] = $bg_image;
                    $event['formatted_date'] = date('j F Y', strtotime($event['date']));
                    $event['formatted_time'] = date('H:i', strtotime($event['start_time'])) . ' - ' . date('H:i', strtotime($event['end_time']));
                ?>
                    
                    <div class="temp-event-card" 
                        onclick="openEventModal(<?= htmlspecialchars(json_encode($event), ENT_QUOTES, 'UTF-8') ?>)"
                        onmouseover="this.style.transform='scale(1.02)';"
                        onmouseout="this.style.transform='scale(1)';"
                        style="
                        background: linear-gradient(rgba(255, 255, 255, 0.65), rgba(255, 255, 255, 0.80)), url('<?= htmlspecialchars($bg_image) ?>') center/cover no-repeat;
                        border-radius: 12px; 
                        padding: 20px; 
                        box-shadow: 0 4px 15px rgba(90, 5, 5, 0.05); 
                        border: 1px solid #f0f0f0;
                        display: flex;
                        flex-direction: column;
                        cursor: pointer;
                        transition: transform 0.2s ease;">
                        
                        <h3 style="font-family: 'Montserrat', sans-serif; color: #111; margin-top: 0; font-size: 18px; position: relative; z-index: 2; text-shadow: 0px 1px 3px rgba(255,255,255,0.8);">
                            <?= htmlspecialchars($event['event_name']) ?>
                        </h3>
                        
                        <p style="font-size: 13px; color: #333; margin-bottom: 12px; font-family: 'Montserrat', sans-serif; position: relative; z-index: 2; font-weight: 600;">
                            <i class="fas fa-calendar-alt" style="color: #5a0505;"></i> <?= date('F j, Y', strtotime($event['date'])) ?> | 
                            <i class="fas fa-clock" style="color: #5a0505;"></i> <?= date('g:i A', strtotime($event['start_time'])) ?> - <?= date('g:i A', strtotime($event['end_time'])) ?>
                        </p>
                        
                        <p style="font-size: 14px; color: #222; font-family: 'Montserrat', sans-serif; line-height: 1.5; position: relative; z-index: 2; flex-grow: 1; font-weight: 500;">
                            <?= htmlspecialchars(substr($event['description'], 0, 90)) ?>...
                        </p>
                    </div>
                    
                <?php endwhile; ?>
            <?php else: ?>
                <p style="font-family: 'Montserrat', sans-serif; color: #666;">No upcoming events found. Check back soon!</p>
            <?php endif; ?>

        </div>
    </main>
</div>

<div id="eventDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    
    <div style="background: #ffffff; border-radius: 12px; width: 90%; max-width: 750px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3); display: flex; flex-direction: column;">
        
        <div id="modalEventImage" style="height: 220px; background-size: cover; background-position: center; position: relative;">
            <button type="button" onclick="openGalleryModal(currentEventData)" style="position: absolute; bottom: 15px; right: 15px; background: rgba(255,255,255,0.85); border: none; border-radius: 20px; padding: 6px 16px; font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 600; cursor: pointer; color: #333; z-index: 10;">
                View more <i class="fas fa-chevron-right" style="font-size: 10px; margin-left: 5px;"></i>
            </button>
        </div>

        <div style="padding: 25px; text-align: center;">
            <h2 id="modalEventTitle" style="font-family: 'Montserrat', sans-serif; color: #111; margin: 0 0 5px 0; font-size: 24px; font-weight: 700;">Event Name</h2>
            <p id="modalEventId" style="font-family: 'Montserrat', sans-serif; color: #666; margin: 0 0 20px 0; font-size: 14px;">Event ID</p>

            <div style="border: 1px solid #ccc; border-radius: 8px; padding: 15px; text-align: left; height: 100px; overflow-y: auto; margin-bottom: 20px;">
                <p id="modalEventDesc" style="font-family: 'Montserrat', sans-serif; font-size: 14px; color: #333; margin: 0; line-height: 1.6;">Event Description</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                <div style="background: #4a0404; color: white; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-size: 13px;">
                    <span>Date :</span> <span id="modalEventDate" style="font-weight: 600;">12 January 2026</span>
                </div>
                <div style="background: #4a0404; color: white; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-size: 13px;">
                    <span>Duration:</span> <span id="modalEventDuration" style="font-weight: 600;">2 hours</span>
                </div>
                <div style="background: #4a0404; color: white; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-size: 13px; grid-column: 1 / 2;">
                    <span>Time :</span> <span id="modalEventTime" style="font-weight: 600;">10:00 - 14:00</span>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a id="modalContactLink" href="../pages/contact.php" style="border: 1.5px solid #5a0505; color: #5a0505; background: transparent; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    Contact staff for further inquiries <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
                </a>
                
                <button onclick="closeEventModal()" style="background: #eef0f2; border: none; color: #5a0505; padding: 10px 30px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="galleryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    
    <div style="background: #eef0f2; border-radius: 12px; width: 90%; max-width: 750px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.4); display: flex; flex-direction: column;">
        
        <div class="gallery-scroll-area">
            <div id="masonryContainer" class="masonry-gallery">
                </div>
        </div>

        <div style="padding: 20px; display: flex; justify-content: center; gap: 20px; background: rgba(255,255,255,0.7);">
            <button onclick="backToEventModal()" style="background: #ffffff; border: 1.5px solid #dcdcdc; color: #5a0505; padding: 10px 0; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; width: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                Back
            </button>
            <button onclick="closeGalleryModal()" style="background: #ffffff; border: 1.5px solid #dcdcdc; color: #5a0505; padding: 10px 0; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; width: 140px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                Exit
            </button>
        </div>
        
    </div>
</div>

<script>
let currentEventData = null;

function openEventModal(eventData) {
    currentEventData = eventData;
    // Populate Data
    document.getElementById('modalEventImage').style.backgroundImage = `url('${eventData.cover_image_url}')`;
    document.getElementById('modalEventTitle').innerText = eventData.event_name;
    document.getElementById('modalEventId').innerText = `Event ID: EVT-${eventData.id.toString().padStart(4, '0')}`;
    document.getElementById('modalEventDesc').innerText = eventData.description;
    
    document.getElementById('modalEventDate').innerText = eventData.formatted_date;
    document.getElementById('modalEventTime').innerText = eventData.formatted_time;
    document.getElementById('modalEventDuration').innerText = eventData.calculated_duration;

    // Attach Event ID to the contact link
    document.getElementById('modalContactLink').href = `../pages/contact.php?event_id=${eventData.id}`;

    // Show Modal
    document.getElementById('eventDetailModal').style.display = 'flex';
}

function closeEventModal() {
    document.getElementById('eventDetailModal').style.display = 'none';
}

function openGalleryModal(eventData) {
    if (!eventData || !eventData.id) {
        return;
    }

    // 1. Hide the Details Modal
    document.getElementById('eventDetailModal').style.display = 'none';
    const container = document.getElementById('masonryContainer');

    container.innerHTML = '';

    fetch(`../auth/get_images.php?event_id=${eventData.id}`)
        .then(res => res.json())
        .then(images => {
            if (!images.length) {
                container.innerHTML = '<p style="font-family: \"Montserrat\", sans-serif; color: #666; grid-column: 1 / -1; text-align: center;">No gallery images available for this event.</p>';
                return;
            }

            images.forEach(img => {
                const imageElement = document.createElement('img');
                imageElement.src = img.image_path.startsWith('../') || img.image_path.startsWith('/')
                    ? img.image_path
                    : `../${img.image_path}`;
                imageElement.loading = "lazy";
                container.appendChild(imageElement);
            });
        });

    document.getElementById('galleryModal').style.display = 'flex';
}

function backToEventModal() {
    // Hide Gallery, bring back Details
    document.getElementById('galleryModal').style.display = 'none';
    document.getElementById('eventDetailModal').style.display = 'flex';
}

function closeGalleryModal() {
    // Completely exit the viewer
    document.getElementById('galleryModal').style.display = 'none';
}

</script>

<?php include '../components/faq_modal.php'; ?>
</body>
</html>