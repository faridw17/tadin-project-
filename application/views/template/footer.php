</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <div class="text-muted">Copyright &copy; <?= $judul ?> <?= date("Y") ?></div>
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
<script src="<?= base_url() ?>node_modules/startbootstrap-sb-admin-2/js/sb-admin-2.min.js"></script>

<script>
    function logout() {
        Swal.fire({
            title: "Peringatan",
            text: "Apakah Anda yakin logout?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Iya",
            cancelButtonText: "Tidak",
        }).then(function(result) {
            if (result.value) {
                window.location.replace('<?= base_url() ?>auth/logout')
            }
        });
    }
</script>
</body>

</html>