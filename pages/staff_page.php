<?php
    session_start();
    require_once '../components/config.php'; 

    if (!isset($_SESSION['id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
        header("Location: ../index.php");
        exit();
    }

    require_once '../components/header.php'; 
    require_once '../components/sidebar.php';

    // 2. Role-Based SQL Query
    if ($_SESSION['role'] === 'Admin') {
        $sql_events = "SELECT * FROM event_list ORDER BY date ASC";
        $stmt = $conn->prepare($sql_events);
        $stmt->execute();
        $result_events = $stmt->get_result();
    } else {
        $sql_events = "SELECT * FROM event_list WHERE id = ? ORDER BY date ASC";
        $stmt = $conn->prepare($sql_events);
        $stmt->bind_param("i", $_SESSION['event_id']);
        $stmt->execute();
        $result_events = $stmt->get_result();
    }
?>

<style>
    .gallery-scroll-area { max-height: 65vh; overflow-y: auto; padding: 20px; }
    .gallery-scroll-area::-webkit-scrollbar { width: 8px; }
    .gallery-scroll-area::-webkit-scrollbar-thumb { background: #b0b0b0; border-radius: 4px; }
    .masonry-gallery { column-count: 4; column-gap: 12px; }
    .masonry-gallery img { width: 100%; display: block; margin-bottom: 12px; border-radius: 6px; break-inside: avoid; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
</style>

<div class="main-content">
    <main class="main-content-wrapper" style="padding: 90px 30px 30px 30px;">
        
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
                    
                    $start_dt = new DateTime($event['start_time']);
                    $end_dt = new DateTime($event['end_time']);
                    $diff = $start_dt->diff($end_dt);
                    $duration_str = $diff->h . ' hours';
                    if ($diff->i > 0) { $duration_str .= ' ' . $diff->i . ' mins'; }

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
                        border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(90, 5, 5, 0.05); border: 1px solid #f0f0f0; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.2s ease;">
                        
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

                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); text-align: right; position: relative; z-index: 3;">
                            <button onclick="event.stopPropagation(); openEditModal(<?= htmlspecialchars(json_encode($event), ENT_QUOTES, 'UTF-8') ?>);" style="background: #D4AF37; border: none; color: #111; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                <i class="fas fa-edit"></i> Edit Event
                            </button>
                        </div>

                    </div>
                    <?php endwhile; ?>
            <?php else: ?>
                <p style="font-family: 'Montserrat', sans-serif; color: #666;">No events assigned to your account.</p>
            <?php endif; ?>

        </div>
    </main>
</div>

<div id="eventDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99998; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
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
                    <span>Date :</span> <span id="modalEventDate" style="font-weight: 600;"></span>
                </div>
                <div style="background: #4a0404; color: white; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-size: 13px;">
                    <span>Duration:</span> <span id="modalEventDuration" style="font-weight: 600;"></span>
                </div>
                <div style="background: #4a0404; color: white; padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; font-family: 'Montserrat', sans-serif; font-size: 13px; grid-column: 1 / 2;">
                    <span>Time :</span> <span id="modalEventTime" style="font-weight: 600;"></span>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center;">
                <button id="modalEditBtn" style="border: none; color: #111; background: #D4AF37; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <i class="fas fa-edit"></i> Edit this Event
                </button>
                
                <button onclick="closeEventModal()" style="background: #eef0f2; border: none; color: #5a0505; padding: 10px 30px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div id="editEventModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 12px; width: 90%; max-width: 750px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.4); display: flex; flex-direction: column;">
        
        <div id="editModalImage" style="height: 150px; background-size: cover; background-position: center; position: relative;">
            <div style="position: absolute; inset: 0; background: rgba(90, 5, 5, 0.85); display: flex; align-items: center; justify-content: center;">
                <h2 style="color: white; font-family: 'Montserrat', sans-serif; margin: 0; font-weight: 700;">
                    <i class="fas fa-edit" style="margin-right: 10px; color: #D4AF37;"></i> Edit Event Details
                </h2>
            </div>
        </div>

        <div style="padding: 25px;">
            <form action="../auth/process_edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="editFormId" name="event_id">

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">Event Name</label>
                    <input type="text" id="editFormName" name="event_name" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box;" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">Date</label>
                        <input type="date" id="editFormDate" name="event_date" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box;" required>
                    </div>
                    <div>
                        <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">Start Time</label>
                        <input type="time" id="editFormStart" name="start_time" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box;" required>
                    </div>
                    <div>
                        <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">End Time</label>
                        <input type="time" id="editFormEnd" name="end_time" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box;" required>
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">Description</label>
                    <textarea id="editFormDesc" name="description" rows="3" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box; resize: vertical;" required></textarea>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #333; margin-bottom: 6px;">Add Gallery Image</label>
                    <input type="file" name="event_image" accept="image/*" style="width: 100%; padding: 10px; border: 1.5px solid #ccc; border-radius: 6px; font-family: 'Montserrat', sans-serif; box-sizing: border-box;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <button type="button" onclick="closeEditModal()" style="background: #eef0f2; border: none; color: #333; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600;">Cancel</button>
                    <button type="submit" style="background: #D4AF37; border: none; color: #111; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="galleryModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 99999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background: #eef0f2; border-radius: 12px; width: 90%; max-width: 750px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.4); display: flex; flex-direction: column;">
        <div class="gallery-scroll-area">
            <div id="masonryContainer" class="masonry-gallery"></div>
        </div>
        <div style="padding: 20px; display: flex; justify-content: center; gap: 20px; background: rgba(255,255,255,0.7);">
            <button onclick="backToEventModal()" style="background: #ffffff; border: 1.5px solid #dcdcdc; color: #5a0505; padding: 10px 0; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; width: 140px;">Back</button>
            <button onclick="closeGalleryModal()" style="background: #ffffff; border: 1.5px solid #dcdcdc; color: #5a0505; padding: 10px 0; border-radius: 8px; cursor: pointer; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700; width: 140px;">Exit</button>
        </div>
    </div>
</div>

<script>
let currentEventData = null;

// ---------- DETAILS MODAL LOGIC ----------
function openEventModal(eventData) {
    currentEventData = eventData;
    document.getElementById('modalEventImage').style.backgroundImage = `url('${eventData.cover_image_url}')`;
    document.getElementById('modalEventTitle').innerText = eventData.event_name;
    document.getElementById('modalEventId').innerText = `Event ID: EVT-${eventData.id.toString().padStart(4, '0')}`;
    document.getElementById('modalEventDesc').innerText = eventData.description;
    
    document.getElementById('modalEventDate').innerText = eventData.formatted_date;
    document.getElementById('modalEventTime').innerText = eventData.formatted_time;
    document.getElementById('modalEventDuration').innerText = eventData.calculated_duration;

    // THE FIX: Attach the eventData to the Edit Button inside the modal
    const editBtn = document.getElementById('modalEditBtn');
    editBtn.onclick = function() {
        openEditModal(eventData);
    };

    document.getElementById('eventDetailModal').style.display = 'flex';
}

function closeEventModal() {
    document.getElementById('eventDetailModal').style.display = 'none';
}

// ---------- EDIT MODAL LOGIC (NEW) ----------
function openEditModal(eventData) {
    // 1. Hide the detail modal if it was open
    closeEventModal();
    
    // 2. Pre-fill the form inputs with the database values
    document.getElementById('editFormId').value = eventData.id;
    document.getElementById('editFormName').value = eventData.event_name;
    document.getElementById('editFormDate').value = eventData.date;
    document.getElementById('editFormStart').value = eventData.start_time;
    document.getElementById('editFormEnd').value = eventData.end_time;
    document.getElementById('editFormDesc').value = eventData.description;

    // 3. Set the background image for the top of the edit modal
    document.getElementById('editModalImage').style.backgroundImage = `url('${eventData.cover_image_url}')`;

    // 4. Show the edit modal
    document.getElementById('editEventModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editEventModal').style.display = 'none';
}

// ---------- GALLERY MODAL LOGIC ----------
function openGalleryModal(eventData) {
    if (!eventData || !eventData.id) {
        return;
    }

    document.getElementById('eventDetailModal').style.display = 'none';
    const container = document.getElementById('masonryContainer');
    container.innerHTML = '';
    
    // Dynamically load images for this event_id
    fetch(`../auth/get_images.php?event_id=${eventData.id}`)
    .then(res => res.json())
    .then(images => {
        if (!images.length) {
            container.innerHTML = '<p style="font-family: \"Montserrat\", sans-serif; color: #666; grid-column: 1 / -1; text-align: center;">No gallery images available for this event.</p>';
            return;
        }

        images.forEach(img => {
            const imgElement = document.createElement('img');
            imgElement.src = img.image_path.startsWith('../') || img.image_path.startsWith('/')
                ? img.image_path
                : `../${img.image_path}`;
            imgElement.loading = "lazy";
            container.appendChild(imgElement);
        });
    });
    document.getElementById('galleryModal').style.display = 'flex';
}

function backToEventModal() {
    document.getElementById('galleryModal').style.display = 'none';
    document.getElementById('eventDetailModal').style.display = 'flex';
}

function closeGalleryModal() {
    document.getElementById('galleryModal').style.display = 'none';
}
</script>
</body>
</html>