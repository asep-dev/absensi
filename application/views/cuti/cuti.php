<!-- Main Content -->
<div class="hk-pg-wrapper">
    <div class="container-xxl">

        <?php $this->load->view('layout/breadcrumbs') ?>

        <div class="hk-page-body">
            <div class="row">
                <div class="col-md-12 mb-md-4 mb-3">
                    <div class="card card-border mb-0 h-100">
                        <div class="card-header card-header-action">
                            <h6>Semua cuti</h6>
                            <?php if($this->session->userdata('role_id') == 1) : ?>
                                <div class="card-action-wrap">
                                    <button class="btn btn-sm btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#modal">
                                        <span>
                                            <span class="icon">
                                                <span class="feather-icon">
                                                    <i data-feather="plus"></i>
                                                </span>
                                            </span>
                                            <span class="btn-text">Ajukan</span>
                                        </span>
                                    </button>
                                </div>
                            <?php endif;?>
                        </div>
                        <div class="card-body p-3 pt-4">
                            <div class="contact-list-view">
                                <table id="table-cuti" class="table nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama lengkap</th>
                                            <th>Kategori Cuti</th>
                                            <th>Tgl Pengajuan</th>
                                            <th>Tgl Mulai</th>
                                            <th>Tgl Berakhir</th>
                                            <th>Jml Hari</th>
                                            <th>Status</th>
                                            <th>Alasan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        foreach($list_cuti as $cuti) :?>
                                            <tr class="fs-7">
                                                <td scope="row"><?= $no++ ?></td>
                                                <td>
                                                    <div class="media align-items-center">
                                                        <div class="media-head me-2">
                                                            <div class="avatar avatar-xs avatar-rounded">
                                                                <img src="<?= base_url()?>public/image/users/<?= $cuti['image_profile'] ?>" alt="user" class="avatar-img">
                                                            </div>
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="text-high-em"><?= $cuti['full_name'] ?></div> 
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                <?php if($cuti['cuti_type'] == 'CS') :?>
                                                    Cuti Sakit
                                                <?php elseif($cuti['cuti_type'] == 'CI') :?>
                                                    Cuti Izin / Penting
                                                <?php elseif($cuti['cuti_type'] == 'CT') :?>
                                                    Cuti Tahunan
                                                <?php elseif($cuti['cuti_type'] == 'CSS') :?>
                                                    Cuti Sakit (Surat)
                                                <?php endif; ?>
                                                </td>
                                                <td><?= date('d M Y', strtotime($cuti['submission_date'])) ?></td>
                                                <td>
                                                    <span class="badge badge-soft-success">
                                                        <?= date('d M Y', strtotime($cuti['start_date'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-soft-danger">
                                                        <?= date('d M Y', strtotime($cuti['end_date'])) ?>
                                                    </span>
                                                </td>
                                                <td><?= $cuti['number_of_days'] ?> Hari</td>
                                                <td>
                                                    <?php if($cuti['cuti_status'] == 'P') :?>
                                                        <span class="badge badge-soft-warning">
                                                            Sedang di proses
                                                        </span>
                                                    <?php elseif($cuti['cuti_status'] == 'A') : ?>
                                                        <span class="badge badge-soft-success">
                                                            Disetujui
                                                        </span>
                                                    <?php elseif($cuti['cuti_status'] == 'R') : ?>
                                                        <span class="badge badge-soft-danger">
                                                            Ditolak
                                                        </span>
                                                    <?php elseif($cuti['cuti_status'] == 'AC') : ?>
                                                        <span class="badge badge-soft-success">
                                                            Disetujui & Dirubah
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-soft-dark show-reason" data-reason="<?= $cuti['cuti_reason'] ?>" data-bs-toggle="modal" data-bs-target="#modal-show" type="button">
                                                        <span class="fs-8">
                                                            <span class="icon fs-8">
                                                                <i class="icon dripicons-preview"></i>
                                                            </span>
                                                            <span>Lihat</span>
                                                        </span>
                                                    </button>
                                                    <?php if($cuti['cuti_type'] == "CSS" && $cuti['cuti_file_letter'] !== null):?>
                                                        <button class="btn btn-sm btn-soft-dark show-letter" data-letter="<?= base_url('public/image/letter/') ?><?= $cuti['cuti_file_letter'] ?>" data-bs-toggle="modal" data-bs-target="#modal-show-letter" type="button">
                                                        <span class="fs-8">
                                                            <span class="icon fs-8">
                                                                <i class="icon dripicons-preview"></i>
                                                            </span>
                                                            <span>Lihat Surat</span>
                                                        </span>
                                                    </button>
                                                    <?php elseif($cuti['cuti_type'] == "CSS" && $cuti['cuti_file_letter'] === null) : ?>
                                                        <span class="badge badge-soft-warning">
                                                            Belum upload
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <?php 
                                                $disable = $cuti['cuti_status'] != 'P' ? 'disabled': '';

                                                if($this->session->userdata('role_id') == 2 ) :?>
                                                <td>
                                                    <button <?= $disable ?> class="btn btn-icon btn-sm btn-primary btn-status" data-cutiid="<?= $cuti['cuti_id'] ?>" data-status='2' type="button">
                                                        <span class="icon fs-8">
                                                            <i class="icon dripicons-checkmark"></i>
                                                        </span>
                                                    </button>
                                                    <?php if($cuti['cuti_type'] == 'CT') :?>
                                                        <button <?= $disable ?> class="btn btn-icon btn-sm btn-soft-dark edit-cuti" data-cutiid="<?= $cuti['cuti_id'] ?>" data-start="<?= $cuti['start_date'] ?>" data-end="<?= $cuti['end_date'] ?>" data-total="<?= $cuti['total_day'] ?>" data-reason="<?= $cuti['cuti_reason'] ?>" data-bs-toggle="modal" data-bs-target="#modal-edit" type="button">
                                                            <span class="icon fs-8">
                                                                <i class="icon dripicons-pencil"></i>
                                                            </span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <button <?= $disable ?> class="btn btn-icon btn-sm btn-soft-danger btn-status" data-cutiid="<?= $cuti['cuti_id'] ?>" data-status="3" type="button">
                                                        <span class="icon fs-8">
                                                            <i class="icon dripicons-cross"></i>
                                                        </span>
                                                    </button>
                                                </td>
                                                <?php endif; ?>
                                                <?php if($this->session->userdata('role_id') == 1 ) :?>
                                                    <td class="text-center">
                                                        <?php if($cuti["cuti_type"] == "CSS"): ?>
                                                            <button class="btn btn-sm btn-soft-primary upload-letter" type="button" data-cutiid="<?= $cuti['cuti_id'] ?>" data-bs-toggle="modal" data-bs-target="#modal-upload">
                                                                <span class="fs-8">
                                                                    <span class="icon fs-8">
                                                                        <i class="icon dripicons-upload"></i>
                                                                    </span>
                                                                    <span>Upload Surat</span>
                                                                </span>
                                                            </button>
                                                        <?php endif;?>
                                                        <?php if($cuti['cuti_status'] == "P" ) :?>
                                                            <button class="btn btn-sm btn-soft-danger remove" type="button" data-cutiid="<?= $cuti['cuti_id'] ?>">
                                                                <span class="fs-8">
                                                                    <span class="icon fs-8">
                                                                        <i class="icon dripicons-trash"></i>
                                                                    </span>
                                                                    <span>Hapus</span>
                                                                </span>
                                                            </button>
                                                        <?php endif; ?>
                                                        <?php if($cuti['cuti_status'] != "P" && $cuti['cuti_type'] != "CSS") : ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view('layout/footer-copyright') ?>
    </div>
</div>

<?php if($this->session->userdata('role_id') == 1) : ?>
<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Ajukan Cuti</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="tambah-cuti" class="form-floating" action="<?= base_url('cuti/add_cuti')?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div id="errors" style="display: none;"></div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="fullname" class="fs-8 mb-1">Nama lengkap</label>
                            <div class="input-group">
                                <span class="input-affix-wrapper">
                                    <span class="input-prefix">
                                        <span class="feather-icon">
                                            <i data-feather="user"></i>
                                        </span>
                                    </span>
                                    <input disabled type="text" class="form-control" value="<?= $employee['full_name'] ?>">
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="cuti" class="fs-8 mb-1">Kategori Cuti
                                <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                            </label>
                            <select class="form-select" name="cuti">
                                <option value="0" hidden>Choose...</option>
                                <option value="1" >Cuti Sakit</option>
                                <option value="4" >Cuti Sakit (Surat)</option>
                                <option value="2" >Cuti Izin / Penting</option>
                                <option value="3" >Cuti Tahunan</option>
                            </select>
                        </div>
                        <div id="upload-surat" class="col-12">
                            
                        </div>
                        <div class="col-6">
                            <label for="end-date" class="fs-8 mb-1">Mulai Cuti
                                <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-affix-wrapper">
                                    <span class="input-prefix">
                                        <span class="feather-icon">
                                            <i data-feather="calendar"></i>
                                        </span>
                                    </span>
                                    <input id="start-date" type="text" name="start_date" class="form-control date">
                                </span>
                            </div>
                        </div>

                        <div class="col-6">
                            <label for="start-date" class="fs-8 mb-1">Berakhir Cuti
                                <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-affix-wrapper">
                                    <span class="input-prefix">
                                        <span class="feather-icon">
                                            <i data-feather="calendar"></i>
                                        </span>
                                    </span>
                                    <input id="end-date" type="text" name="end_date" class="form-control date">
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <p class="fs-8">Catatan :</p>
                            <p class="fs-8 mb-1">- Jika ingin ambil cuti 1 hari <strong>(Tgl Mulai & Berakhir Cuti)</strong> masih di tanggal yang sama, Dan tanggal selanjutnya anda diwajibkan masuk.</p>
                            <p class="fs-8">- Untuk cuti sakit (surat) karyawan wajib upload surat keterangan dokter, upload surat bisa dilakukan kapan saja, mau itu saat pengajuan ataupun setelah sembuh, anda bisa upload surat saat pengajuan atau terdapat dibagian <strong>kolom (action)</strong> setelah anda mengajukan cuti dan cuti sakit (surat) tidak akan mengurangi sisa cuti anda.</p>
                        </div>
                        <div class="col-12 mt-3">
                            <div class="form-floating">
                                <textarea id="floatingInputInvalid" class="form-control" name="reason"></textarea>
                                <label for="floatingInputInvalid" class="fs-7">Alasan Cuti
                                    <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                                </label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer p-2" style="border-top: none ;">
                    <input id="csrf" type="hidden" name="<?= $this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>" />
                    <input type="hidden" name="employee_id" value="<?= $employee['employee_id'] ?>">
                    <div class="d-flex me-auto">
                        <div class="me-2 px-3 border border-2 border-light rounded-3">
                            <span class="fs-8">Sisa Cuti</span>
                            <h6 class="mb-0 fw-bold"><?= $employee['remaining_days_off'] ?></h6>
                        </div>
                        <div class="px-3 border border-2 border-light rounded-3">
                            <span class="fs-8">Total Hari</span>
                            <h6 id="hari" class="mb-0 fw-bold">-</h6>
                        </div>
                    </div>
                    <button type="button" class="btn btn-soft-dark fs-7" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary fs-7">Ajukan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($this->session->userdata('role_id') == 2) : ?>
<!-- Modal edit-->
<div class="modal fade" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Edit Cuti</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="edit-cuti" class="form-floating">
                <div class="modal-body">
                    <div id="errors-e" style="display: none;"></div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="end-date" class="fs-8 mb-1">Mulai Cuti
                                <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-affix-wrapper">
                                    <span class="input-prefix">
                                        <span class="feather-icon">
                                            <i data-feather="calendar"></i>
                                        </span>
                                    </span>
                                    <input id="start-date" type="text" name="start_date" class="form-control date">
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="start-date" class="fs-8 mb-1">Berakhir Cuti
                                <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                            </label>
                            <div class="input-group">
                                <span class="input-affix-wrapper">
                                    <span class="input-prefix">
                                        <span class="feather-icon">
                                            <i data-feather="calendar"></i>
                                        </span>
                                    </span>
                                    <input id="end-date" type="text" name="end_date" class="form-control date">
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <p class="fs-8">Catatan :</p>
                            <p class="fs-8">Jika ingin ambil cuti 1 hari <strong>(Tgl Mulai & Berakhir Cuti)</strong> masih di tanggal yang sama, Dan tanggal selanjutnya anda diwajibkan masuk.</p>
                        </div>

                        <div class="col-12 mt-3">
                            <textarea id="reason" disabled class="form-control" rows="2"></textarea>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer p-2" style="border-top: none ;">
                    <input id="cuti" type="hidden">
                    <input id="csrf-e" type="hidden" name="<?= $this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>" />
                    <div class="d-flex me-auto">
                        <div class="px-3 me-2 border border-2 border-light rounded-3">
                            <span class="fs-8">Sisa Cuti</span>
                            <h6 id="total" class="mb-0 fw-bold">-</h6>
                        </div>
                        <div class="px-3 border border-2 border-light rounded-3">
                            <span class="fs-8">Total Hari</span>
                            <h6 id="hari" class="mb-0 fw-bold">-</h6>
                        </div>
                    </div>
                    <button type="button" class="btn btn-soft-dark fs-7" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary fs-7">Update Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal upload -->
<div class="modal fade" id="modal-upload" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <form id="upload-letter" class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Upload Surat</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="errors-u" style="display: none;"></div>
                <label class="fs-8 mb-1">Allow : JPG, PNG, JPEG | Max : 500Kb
                    <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                </label>
                <input class="form-control" type="file" name="image_letter" accept="image/png, image/jpg, image/jpeg">
            </div>
            <div class="modal-footer p-2" style="border-top: none ;">
                <input id="target" type="hidden">
                <input id="csrf-u" type="hidden" name="<?= $this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>" />
                <button type="button" class="btn btn-soft-dark fs-7" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary fs-7">Simpan</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Show Reason -->
<div class="modal fade" id="modal-show" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Alasan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="cuti-reason" class="fs-7"></p>
            </div>
            <div class="modal-footer p-2" style="border-top: none ;">
                <button type="button" class="btn btn-primary fs-7" data-bs-dismiss="modal">Ok, Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Show image -->
<div class="modal fade" id="modal-show-letter" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <img src="" alt="surat">
        </div>
    </div>
</div>

    <!-- Data Table JS -->
<script src="<?= base_url()?>public/assets/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url()?>public/assets/vendors/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= base_url()?>public/assets/vendors/datatables.net-select/js/dataTables.select.min.js"></script>
<!-- Daterangepicker JS -->
<script src="<?= base_url()?>public/assets/vendors/moment/min/moment.min.js"></script>
<script src="<?= base_url()?>public/assets/vendors/daterangepicker/daterangepicker.js"></script>
<script>
    $(document).ready( function () {
        
		$('.date').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: <?= date('Y', strtotime('-1 year')) ?>,
            maxYear: <?= date('Y', strtotime('+1 year')) ?>,
            "cancelClass": "btn-soft-dark",
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
        
        
        $('#table-cuti').DataTable({
            scrollX:  true,
            language: { search: "",
                searchPlaceholder: "Search",
                sLengthMenu: "_MENU_items",
                paginate: {
                    next: '<i class="ri-arrow-right-s-line"></i>', // or '→'
                    previous: '<i class="ri-arrow-left-s-line"></i>' // or '←' 
                }
            },
            "drawCallback": function () {
                $('.dataTables_paginate > .pagination').addClass('custom-pagination pagination-simple pagination-sm');
            }
        });

        $('select[name="cuti"]').change(function (e) { 
            e.preventDefault();
            if (this.value == 4) {
                $('#upload-surat').html(`
                <label for="cuti" class="fs-8 mb-1">Upload Surat | JPG, PNG, JPEG | Max : 500Kb </label>
                <input class="form-control" type="file" name="image" accept="image/png, image/jpg">
                `)
            } else {
                $('#upload-surat').html('')
            }
        });

        $(document).on('click', '.show-reason', function(e){
            e.preventDefault()
            const reason = $(this).data('reason')

            $('#modal-show .modal-body #cuti-reason').html(reason)
            
        })

        $(document).on('click', '.show-letter', function(e){
            e.preventDefault()
            const letter = $(this).data('letter')

            $('#modal-show-letter img').attr('src', letter)
            
        })

        $(document).on('click', '.edit-cuti', function(e){
            e.preventDefault()
            const cutiId = $(this).data('cutiid')
            const startDate = $(this).data('start')
            const endDate = $(this).data('end')
            const reason = $(this).data('reason')
            const totalday = $(this).data('total')

            $('#modal-edit #cuti').val(cutiId)
            $('#modal-edit #start-date').val(startDate)
            $('#modal-edit #end-date').val(endDate)
            $('#modal-edit #reason').html(reason)
            $('#modal-edit #total').html(totalday)
            
            $('#start-date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate: startDate,
                minYear: <?= date('Y', strtotime('-1 year')) ?>,
                maxYear: <?= date('Y', strtotime('+1 year')) ?>,
                "cancelClass": "btn-soft-dark",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $('#end-date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate: endDate,
                minYear: <?= date('Y', strtotime('-1 year')) ?>,
                maxYear: <?= date('Y', strtotime('+1 year')) ?>,
                "cancelClass": "btn-soft-dark",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        })

        $(document).on('click', '.upload-letter', function(e){
            e.preventDefault()
            const cutiId = $(this).data('cutiid')
            $('#modal-upload #target').val(cutiId)

        })

        $(document).on('click', '.btn-status', function(e){
            const cutiId = $(this).data('cutiid')
            const cutiStatus = $(this).data('status')
            const url = `<?= base_url('cuti/approve_reject/') ?>${cutiId}/${cutiStatus}`
            const csrfName = '<?= $this->security->get_csrf_token_name()?>'
            const csrfHash = '<?= $this->security->get_csrf_hash()?>'
            // console.log(url)

            Swal.fire({
                html:
                `<div class="avatar avatar-icon avatar-soft-warning mb-3">
                    <span class="initial-wrap rounded-8">
                        <i class="icon dripicons-information" style="line-height:0;"></i>
                    </span>
                </div>
                <div>
                    <h5 class="text-dark">Konfirmasi</h5>
                    <p class="fs-7 mt-2">Anda yakin ingin memverifikasi karyawan ini?</p>
                </div>`,
                customClass: {
                    content: 'p-3 text-center',
                    confirmButton: 'btn btn-primary fs-7',
                    actions: 'justify-content-center mt-1 p-0',
                    cancelButton:'btn btn-soft-dark fs-7 me-2'
                },
                width: 300,
                confirmButtonText: 'Ya, Konfimasi',
                cancelButtonText: 'Tutup',
                reverseButtons:true,
                showCancelButton: true,
                buttonsStyling: false,
            }).then((r)=>{
                if(r.value){
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {[csrfName]:csrfHash},
                        dataType: "json",  
                        cache: false,
                        success: function(response){
                            if (response.success) {
                                sweatalert_confirm('success', response)
                            }
                        },
                        error : function(xhr, status, errorThrown){
                            console.log(xhr.responseText)
                            console.log(status)
                            console.log(errorThrown)
                        }
                    });
                }
            })
            return false;
        })

        $(document).on('submit', '#tambah-cuti', function(e){
            e.preventDefault()
            const url = $(this).attr('action');
            var data;

            if ($('input[name="image"]').val()) {
                data = new FormData();
                $(this).serializeArray().forEach(function(e) {
                    data.append(e.name, e.value)
                })
                data.append( 'image', $('input[name="image"]')[0].files[0]);

            } else {
                data = new FormData(this)
            }
            // for (const j of data.entries()) {
            //     console.log(j)
            // }
            $.ajax({
                type: "POST",
                url: url, 
                data: data,
                dataType: "json",  
                cache: false,
                contentType: false,
                processData: false,
                success: function(response){
                    if (response.success) {
                        sweatalert_confirm('success', response)
                    }

                    if (response.errors) {
                        $('#errors').html(response.desc).show()
                        $('#csrf').val(response.csrfHash)
                    }

                    if (response.error) {
                        sweatalert_confirm('danger', response)
                    }

                    if (response.warning) {
                        sweatalert_confirm('warning', response)
                    }
                },
                error : function(xhr, status, errorThrown){
                    console.log(xhr.responseText)
                    console.log(status)
                    console.log(errorThrown)
                }
            });
            return false;
        });
        
        $(document).on('submit', '#upload-letter', function(e){
            e.preventDefault()
            const url = '<?= base_url('cuti/upload_letter/') ?>';
            const data = new FormData();
            $(this).serializeArray().forEach(function(e) {
                data.append(e.name, e.value)
            })
            data.append( 'image', $('input[name="image_letter"]')[0].files[0]);
            // for (const j of data.entries()) {
            //     console.log(j)
            // }
            $.ajax({
                type: "POST",
                url: url + $('#target').val(), 
                data: data,
                dataType: "json",  
                cache: false,
                contentType: false,
                processData: false,
                success: function(response){
                    if (response.success) {
                        sweatalert_confirm('success', response)
                    }

                    if (response.errors) {
                        $('#errors-u').html(response.desc).show()
                        $('#csrf-u').val(response.csrfHash)
                    }

                    if (response.error) {
                        sweatalert_confirm('danger', response)
                    }

                    if (response.warning) {
                        sweatalert_confirm('warning', response)
                    }
                },
                error : function(xhr, status, errorThrown){
                    console.log(xhr.responseText)
                    console.log(status)
                    console.log(errorThrown)
                }
            });
            return false;
        });

        $(document).on('submit', '#edit-cuti', function(e){
            e.preventDefault()

            const cutiId = $(this).find('#cuti').val()
            const cutiStatus = 4;
            const url = `<?= base_url('cuti/approve_reject/') ?>${cutiId}/${cutiStatus}`
            console.log(url)
            $.ajax({
                type: "POST",
                url: url,
                data: $(this).serialize(),
                dataType: "json",  
                cache: false,
                success: function(response){
                    if (response.success) {
                        sweatalert_confirm('success', response)
                    }
                    if (response.errors) {
                        $('#errors-e').html(response.desc).show()
                        $('#csrf-e').val(response.csrfHash)
                    }

                    if (response.warning) {
                        sweatalert_confirm('warning', response)
                    }
                },
                error : function(xhr, status, errorThrown){
                    console.log(xhr.responseText)
                    console.log(status)
                    console.log(errorThrown)
                }
            });
            return false;
        })

        Date.prototype.addDays = function( days ) {
            var date = new Date(this.valueOf())
            date.setDate(date.getDate() + days);
            return date;
        }

        const getBusinessDatesCount = function(startDate, endDate) {
            var count = 0;
            var curDate = startDate;
            while (curDate <= endDate) {
                var dayOfWeek = curDate.getDay();
                var isWeekend = (dayOfWeek == 6) || (dayOfWeek == 0); 
                if(!isWeekend)count++;
                curDate = curDate.addDays(1);
            }
            return count;
        }

        const countDay = function(){
            let startDate = new Date($('#start-date').val())
            let endDate = new Date($('#end-date').val())
            let result = getBusinessDatesCount(startDate, endDate)

            $('#start-date').change(function (e) { 
                e.preventDefault();
                startDate = new Date($('#start-date').val())
                result = getBusinessDatesCount(startDate, endDate)
                $('#hari').text(result)
            });
            
            $('#end-date').change(function (e) { 
                e.preventDefault();
                endDate = new Date($('#end-date').val())
                result = getBusinessDatesCount(startDate, endDate)
                $('#hari').text(result)
            });
            $('#hari').text(result)
        }
        countDay()

        const sweatalert_confirm = function(type, res){
            let icon;
            let classHeader;
            if (type == 'success') {
                icon = '😍';
                classHeader = 'avatar-soft-success';
            } else if(type == 'danger'){
                icon = '😣';
                classHeader = 'avatar-soft-danger';
            } else if(type == 'warning'){
                icon = '🧐';
                classHeader = 'avatar-soft-warning';
            }

            Swal.fire({
                html:
                `<div class="avatar avatar-icon ${classHeader}  mb-3">
                    <span class="initial-wrap rounded-8">
                        ${icon}
                    </span>
                </div>
                <div>
                    <h5 class="text-dark">${res.title}</h5>
                    <p class="fs-7 mt-2">${res.desc}</p>
                </div>`,
                customClass: {
                    content: 'p-3 text-center',
                    confirmButton: 'btn btn-primary fs-7',
                    actions: 'justify-content-center mt-1',
                },
                width: 300,
                confirmButtonText: res.buttontext,
                buttonsStyling: false,
            }).then((r) => {
                setTimeout(() => {
                    location.reload()
                }, 10);
            })
        }
    });

</script>

<script>
    $(document).ready(function () {
        $(document).on('click', '.remove', function () {
            const csrfName = '<?= $this->security->get_csrf_token_name() ?>';
            const csrfHash = '<?= $this->security->get_csrf_hash() ?>';
            const target = $(this).data('cutiid')

            Swal.fire({
                html:
                `<div class="avatar avatar-icon avatar-soft-danger mb-3">
                    <span class="initial-wrap rounded-8">
                        <i class="icon dripicons-trash" style="line-height:0;"></i>
                    </span>
                </div>
                <div>
                    <h5 class="text-dark">Hapus</h5>
                    <p class="fs-7 mt-2">Anda yakin ingin menghapus ini?</p>
                </div>`,
                customClass: {
                    content: 'p-3 text-center',
                    confirmButton: 'btn btn-danger fs-7',
                    actions: 'justify-content-center mt-1 p-0',
                    cancelButton:'btn btn-soft-dark fs-7 me-2'
                },
                width: 300,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Tutup',
                reverseButtons:true,
                showCancelButton: true,
                buttonsStyling: false,
            }).then((r)=>{
                if(r.value){
                    $.ajax({
                        type: "POST",
                        url: '<?= base_url('cuti/delete_cuti/') ?>' + target, 
                        data: {[csrfName] : csrfHash},
                        dataType: "json",  
                        cache: false,
                        success: function(response){
                            if (response.error) {
                                sweatalert_confirm('danger', response)
                            }
                            if (response.success) {
                                sweatalert_confirm('success', response)
                            }
                        },
                        error : function(xhr, status, errorThrown){
                            console.log(xhr.responseText)
                            console.log(status)
                            console.log(errorThrown)
                        }
                    });
                }
            })
        });
    });

    const sweatalert_confirm = function(type, res){
        let icon;
        let classHeader;
        if (type == 'success') {
            icon = '😍';
            classHeader = 'avatar-soft-success';
        } else if(type == 'danger'){
            icon = '😣';
            classHeader = 'avatar-soft-danger';
        } else if(type == 'warning'){
            icon = '🧐';
            classHeader = 'avatar-soft-warning';
        }

        Swal.fire({
            html:
            `<div class="avatar avatar-icon ${classHeader}  mb-3">
                <span class="initial-wrap rounded-8">
                    ${icon}
                </span>
            </div>
            <div>
                <h5 class="text-dark">${res.title}</h5>
                <p class="fs-7 mt-2">${res.desc}</p>
            </div>`,
            customClass: {
                content: 'p-3 text-center',
                confirmButton: 'btn btn-primary fs-7',
                actions: 'justify-content-center mt-1',
            },
            width: 300,
            confirmButtonText: res.buttontext,
            buttonsStyling: false,
        }).then((r) => {
            setTimeout(() => {
                location.reload()
            }, 10);
        })
    }
</script>