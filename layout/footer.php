<style>
.footer-responsive {
  padding: 2.5rem 0;
}

@media (max-width: 1200px) {
  .footer-responsive {
    padding: 1.25rem 0;
  }
}

@media (max-width: 768px) {
  .footer-responsive {
    padding: 1rem 0;
  }
  
  .footer-responsive .container {
    padding-left: 12px;
    padding-right: 12px;
  }
  
  .footer-responsive .row {
    text-align: center;
  }
  
  .footer-responsive .col-md-6 {
    margin-bottom: 0.5rem;
  }
  
  .footer-responsive .col-md-6:last-child {
    margin-bottom: 0;
  }
}

@media (max-width: 576px) {
  .footer-responsive {
    padding: 0.75rem 0;
  }
  
  .footer-responsive .container {
    padding-left: 8px;
    padding-right: 8px;
  }
  
  .footer-responsive small {
    font-size: 0.75rem;
  }
}
</style>

<footer class="bg-dark text-white text-center footer-responsive mt-auto" style="background: #1a1a1a !important; border-top: 1px solid #333;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start">
                <small class="d-flex align-items-center justify-content-center justify-content-md-start" style="color: rgba(255, 255, 255, 0.7);">
                    <i class="fas fa-graduation-cap me-2"></i>
                    © <?= date('Y'); ?> Sistem Akademik SMK
                </small>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="d-flex align-items-center justify-content-center justify-content-md-end" style="color: rgba(255, 255, 255, 0.7);">
                    <i class="fas fa-code me-2"></i>
                    Powered by Abrar
                </small>
            </div>
        </div>
    </div>
</footer>
