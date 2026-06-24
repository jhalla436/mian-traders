<?php $__env->startSection('content'); ?>
  <style>
    .brochure-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
      gap: 24px;
      margin-top: 20px;
    }
    .company-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .company-card-header {
      background: #f8fafc;
      border-bottom: 1px solid #e5e7eb;
      padding: 16px;
    }
    .company-card-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: #1e293b;
      margin: 0;
    }
    .company-card-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .brochures-list {
      list-style: none;
      padding: 0;
      margin: 0 0 20px 0;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .brochure-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #f8fafc;
      border: 1px solid #f1f5f9;
      border-radius: 6px;
      padding: 10px 12px;
      gap: 12px;
    }
    .brochure-info {
      display: flex;
      align-items: center;
      gap: 10px;
      overflow: hidden;
    }
    .brochure-icon {
      flex-shrink: 0;
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .brochure-icon.pdf {
      background: #fef2f2;
      color: #ef4444;
    }
    .brochure-icon.image {
      background: #ecfdf5;
      color: #10b981;
    }
    .brochure-details {
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .brochure-name {
      font-size: 0.875rem;
      font-weight: 600;
      color: #334155;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }
    .brochure-date {
      font-size: 0.75rem;
      color: #64748b;
    }
    .brochure-actions {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0;
    }
    .upload-form-section {
      border-top: 1px solid #f1f5f9;
      padding-top: 16px;
    }
    .upload-form-title {
      font-size: 0.85rem;
      font-weight: 700;
      color: #475569;
      margin: 0 0 10px 0;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .empty-state {
      color: #94a3b8;
      font-size: 0.875rem;
      text-align: center;
      padding: 30px 10px;
      background: #fafafb;
      border: 1px dashed #e2e8f0;
      border-radius: 6px;
      margin-bottom: 20px;
    }
    
    /* Built-in viewer modal styles */
    .viewer-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15, 23, 42, 0.9);
      backdrop-filter: blur(8px);
      z-index: 1000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }
    .viewer-container {
      background: #0f172a;
      border: 1px solid #1e293b;
      border-radius: 12px;
      width: 100%;
      max-width: 1100px;
      height: 90%;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
    }
    .viewer-header {
      background: #0f172a;
      border-bottom: 1px solid #1e293b;
      color: #f8fafc;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .viewer-title {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 600;
    }
    .viewer-close {
      background: none;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      border-radius: 4px;
      transition: all 0.2s;
    }
    .viewer-close:hover {
      color: #fff;
      background: rgba(255, 255, 255, 0.1);
    }
    .viewer-body {
      flex: 1;
      background: #020617;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: auto;
      padding: 20px;
      position: relative;
    }
    .viewer-iframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 6px;
    }
    .viewer-img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      border-radius: 6px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.7);
      cursor: pointer;
      user-select: none;
      transition: transform 0.2s ease;
    }
    .viewer-img:hover {
      transform: scale(1.005);
    }
    .viewer-nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(15, 23, 42, 0.6);
      color: #fff;
      border: none;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      font-size: 2.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s, transform 0.2s;
      z-index: 1010;
      user-select: none;
    }
    .viewer-nav-btn:hover {
      background: rgba(15, 23, 42, 0.9);
      transform: translateY(-50%) scale(1.1);
    }
    .viewer-nav-btn.prev-btn {
      left: 24px;
    }
    .viewer-nav-btn.next-btn {
      right: 24px;
    }
    
    .viewer-fade-in {
      animation: viewerFadeIn 0.2s ease-out forwards;
    }
    
    @keyframes viewerFadeIn {
      from {
        opacity: 0;
        transform: scale(0.98);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    
    @media (max-width: 768px) {
      .viewer-nav-btn {
        width: 40px;
        height: 40px;
        font-size: 1.8rem;
      }
      .viewer-nav-btn.prev-btn {
        left: 8px;
      }
      .viewer-nav-btn.next-btn {
        right: 8px;
      }
    }
  </style>

  <div class="page-header">
    <h2 class="page-title">Company Brochures & Catalogues</h2>
    <div class="page-actions">
      <span class="text-muted" style="font-size: 0.9rem;">Upload and view digital catalogs for lamination and board suppliers.</span>
    </div>
  </div>

  <div class="brochure-grid">
    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php
        $brochuresArray = $company->brochures->map(function($b) {
            return [
                'id' => $b->id,
                'url' => route('mt.company_brochures.file', $b),
                'name' => $b->file_name,
                'is_pdf' => (bool)(str_contains(strtolower($b->mime_type), 'pdf') || str_ends_with(strtolower($b->file_path), '.pdf'))
            ];
        })->values()->toArray();
        $brochuresJson = json_encode($brochuresArray);
      ?>
      <div class="company-card">
        <div class="company-card-header">
          <h3 class="company-card-title"><?php echo e($company->name); ?></h3>
        </div>
        <div class="company-card-body">
          <?php if($company->brochures->isEmpty()): ?>
            <div class="empty-state">
              No brochures uploaded yet
            </div>
          <?php else: ?>
            <ul class="brochures-list">
              <?php $__currentLoopData = $company->brochures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $brochure): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $isPdf = str_contains(strtolower($brochure->mime_type), 'pdf') || str_ends_with(strtolower($brochure->file_path), '.pdf');
                ?>
                <li class="brochure-item">
                  <div class="brochure-info">
                    <div class="brochure-icon <?php echo e($isPdf ? 'pdf' : 'image'); ?>">
                      <?php if($isPdf): ?>
                        <!-- PDF Icon -->
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                      <?php else: ?>
                        <!-- Image Icon -->
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                      <?php endif; ?>
                    </div>
                    <div class="brochure-details">
                      <span class="brochure-name" title="<?php echo e($brochure->file_name); ?>"><?php echo e($brochure->file_name); ?></span>
                      <span class="brochure-date">Uploaded: <?php echo e($brochure->created_at->format('M d, Y')); ?></span>
                    </div>
                  </div>
                  
                  <div class="brochure-actions">
                    <button type="button" class="btn btn-secondary btn-xs"
                            data-brochures="<?php echo htmlspecialchars($brochuresJson, ENT_QUOTES, 'UTF-8'); ?>"
                            data-index="<?php echo e($index); ?>"
                            onclick="openViewer(this)">
                      View
                    </button>
                    <a href="<?php echo e(route('mt.company_brochures.file', $brochure)); ?>" download="<?php echo e($brochure->file_name); ?>" class="btn btn-secondary btn-xs" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                      Download
                    </a>
                    <form method="POST" action="<?php echo e(route('mt.company_brochures.destroy', $brochure)); ?>" onsubmit="return confirm('Are you sure you want to delete this brochure?')">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                    </form>
                  </div>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php endif; ?>

          <div class="upload-form-section">
            <h4 class="upload-form-title">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="color: #4f46e5;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
              Upload New Brochure
            </h4>
            <form method="POST" action="<?php echo e(route('mt.company_brochures.store')); ?>" enctype="multipart/form-data">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="company_id" value="<?php echo e($company->id); ?>">
              
              <div class="form-group" style="margin-bottom: 8px;">
                <input type="text" name="title" placeholder="Brochure Title (optional)" class="mt-input" style="padding: 6px 10px; font-size: 0.85rem;">
              </div>
              <div class="form-group" style="margin-bottom: 12px;">
                <input type="file" name="files[]" required accept=".pdf,image/*" multiple style="font-size: 0.8rem; width: 100%; border: 1px solid #cbd5e1; padding: 4px; border-radius: 4px;">
                <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">You can select multiple files at once.</div>
              </div>
              <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; font-size: 0.85rem; padding: 6px 0;">
                Upload
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <!-- Built-in Modal media viewer -->
  <div id="brochure-modal" class="viewer-overlay" onclick="closeViewer(event)">
    <button id="viewer-prev-btn" class="viewer-nav-btn prev-btn" onclick="navigateViewer(-1, event)">&lsaquo;</button>
    <button id="viewer-next-btn" class="viewer-nav-btn next-btn" onclick="navigateViewer(1, event)">&rsaquo;</button>
    <div class="viewer-container" onclick="event.stopPropagation()">
      <div class="viewer-header">
        <h3 id="modal-title" class="viewer-title">Brochure View</h3>
        <button class="viewer-close" onclick="closeViewer(event)">&times;</button>
      </div>
      <div class="viewer-body">
        <iframe id="modal-iframe" class="viewer-iframe" style="display: none;"></iframe>
        <img id="modal-img" class="viewer-img" style="display: none;" alt="Brochure page" onclick="navigateViewer(1, event)">
      </div>
    </div>
  </div>

  <script>
    let currentBrochures = [];
    let currentIndex = 0;

    function openViewer(btnElement) {
      try {
        const brochuresRaw = btnElement.getAttribute('data-brochures');
        const indexRaw = btnElement.getAttribute('data-index');

        currentBrochures = JSON.parse(brochuresRaw);
        currentIndex = parseInt(indexRaw, 10) || 0;

        const modal = document.getElementById('brochure-modal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Lock background scrolling

        showActiveBrochure();
        window.addEventListener('keydown', handleKeyPress);
      } catch (e) {
        console.error("Brochure Viewer Error:", e);
        alert("Failed to open brochure viewer: " + e.message);
      }
    }

    function showActiveBrochure() {
      if (!currentBrochures || currentBrochures.length === 0) return;

      const modalTitle = document.getElementById('modal-title');
      const iframe = document.getElementById('modal-iframe');
      const img = document.getElementById('modal-img');
      const prevBtn = document.getElementById('viewer-prev-btn');
      const nextBtn = document.getElementById('viewer-next-btn');

      const brochure = currentBrochures[currentIndex];
      modalTitle.textContent = `${brochure.name} (${currentIndex + 1}/${currentBrochures.length})`;

      if (brochure.is_pdf) {
        iframe.src = brochure.url;
        iframe.style.display = 'block';
        img.style.display = 'none';
        img.src = '';
        
        // Trigger fade-in transition
        iframe.classList.remove('viewer-fade-in');
        void iframe.offsetWidth;
        iframe.classList.add('viewer-fade-in');
      } else {
        img.src = brochure.url;
        img.style.display = 'block';
        iframe.style.display = 'none';
        iframe.src = '';
        
        // Trigger fade-in transition
        img.classList.remove('viewer-fade-in');
        void img.offsetWidth;
        img.classList.add('viewer-fade-in');
      }

      if (currentBrochures.length > 1) {
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
      } else {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
      }
    }

    function navigateViewer(direction, event) {
      if (event) {
        event.stopPropagation();
      }
      if (!currentBrochures || currentBrochures.length <= 1) return;
      currentIndex = (currentIndex + direction + currentBrochures.length) % currentBrochures.length;
      showActiveBrochure();
    }

    function handleKeyPress(event) {
      if (event.key === 'ArrowLeft') {
        navigateViewer(-1, event);
      } else if (event.key === 'ArrowRight') {
        navigateViewer(1, event);
      } else if (event.key === 'Escape') {
        closeViewer(event);
      }
    }

    function closeViewer(event) {
      if (event) {
        event.stopPropagation();
      }
      const modal = document.getElementById('brochure-modal');
      const iframe = document.getElementById('modal-iframe');
      const img = document.getElementById('modal-img');

      iframe.src = '';
      img.src = '';
      iframe.style.display = 'none';
      img.style.display = 'none';
      modal.style.display = 'none';
      document.body.style.overflow = ''; // Unlock background scrolling

      window.removeEventListener('keydown', handleKeyPress);
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('mt.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/company_brochures/index.blade.php ENDPATH**/ ?>