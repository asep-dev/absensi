<!-- Main Content -->
<div class="hk-pg-wrapper">
    <div class="container-xxl">

        <?php $this->load->view('layout/breadcrumbs') ?>

        <div class="hk-page-body">
            <div class="row g-2 g-md-3">
                <div class="col-md-3 mb-3">
                    <div class="card card-border h-100">
                        <div class="card-header">
                            <h6 class="fs-7">Filter laporan</h6>
                            <div class="d-flex">
                                <div class="form-check form-switch mb-0">
                                    <input type="checkbox" class="form-check-input" id="check-tahun">
                                    <label class=" fs-8" for="check">Per /thn</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="filter-absensi" action="<?= base_url('absensi/get_laporan') ?>">
                                <div id="filter-bulan">
                                    <label for="jenis" class="fs-8 mb-1">Pilih Bulan
                                        <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                                    </label>
                                    <select id="select-month" class="form-select mb-2" name="filter_month">
                                        <option value="0" hidden>Choose...</option>
                                        <option value="01-<?= date('Y') ?>"<?php if(1 > date('n')) : ?> disabled <?php endif; ?>>Januari <?= date('Y') ?></option>
                                        <option value="02-<?= date('Y') ?>"<?php if(2 > date('n')) : ?> disabled <?php endif; ?>>Februari <?= date('Y') ?></option>
                                        <option value="03-<?= date('Y') ?>"<?php if(3 > date('n')) : ?> disabled <?php endif; ?>>Maret <?= date('Y') ?></option>
                                        <option value="04-<?= date('Y') ?>"<?php if(4 > date('n')) : ?> disabled <?php endif; ?>>April <?= date('Y') ?></option>
                                        <option value="05-<?= date('Y') ?>"<?php if(5 > date('n')) : ?> disabled <?php endif; ?>>Mei <?= date('Y') ?></option>
                                        <option value="06-<?= date('Y') ?>"<?php if(6 > date('n')) : ?> disabled <?php endif; ?>>Juni <?= date('Y') ?></option>
                                        <option value="07-<?= date('Y') ?>"<?php if(7 > date('n')) : ?> disabled <?php endif; ?>>Juli <?= date('Y') ?></option>
                                        <option value="08-<?= date('Y') ?>"<?php if(8 > date('n')) : ?> disabled <?php endif; ?>>Agustus <?= date('Y') ?></option>
                                        <option value="09-<?= date('Y') ?>"<?php if(9 > date('n')) : ?> disabled <?php endif; ?>>September <?= date('Y') ?></option>
                                        <option value="10-<?= date('Y') ?>"<?php if(10 > date('n')) : ?> disabled <?php endif; ?>>Oktober <?= date('Y') ?></option>
                                        <option value="11-<?= date('Y') ?>"<?php if(11 > date('n')) : ?> disabled <?php endif; ?>>November <?= date('Y') ?></option>
                                        <option value="12-<?= date('Y') ?>"<?php if(12 > date('n')) : ?> disabled <?php endif; ?>>Desember <?= date('Y') ?></option>
                                    </select>
                                </div>
                                <div id="filter-tahun" style="display: none;">
                                    <label for="jenis" class="fs-8 mb-1">Filter tahun
                                        <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                                    </label>
                                    <input id="select-year" class="form-control text-center mb-2" type="text" value="<?= date('Y')?>" readonly>
                                </div>

                                <label for="jenis" class="fs-8 mb-1">Pilih Karyawan
                                    <span class="badge badge-danger mb-1 badge-indicator-processing badge-indicator"></span>
                                </label>
                                <select class="form-select mb-3" name="filter_name">
                                    <option value="0" hidden>Choose...</option>
                                    <?php foreach($all_employee as $employee) :?>
                                        <option value="<?= $employee['employee_id']?>"><?= $employee['full_name']?></option>
                                    <?php endforeach;?>
                                </select>
                                <input id="csrf" type="hidden" name="<?= $this->security->get_csrf_token_name()?>" value="<?=$this->security->get_csrf_hash()?>" />
                                <button class="btn btn-primary fs-7 btn-block" type="submit">
                                    <span>
                                        <span class="icon">
                                            <span class="feather-icon">
                                                <i data-feather="filter"></i>
                                            </span>
                                        </span>
                                        <span>Filter</span>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row g-2 g-md-3 mb-4">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                        <small>Total hadir</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Hadir
                                            </h6>
                                            <div id="hadir" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="zap" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                        <small>Total izin</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Izin
                                            </h6>
                                            <div id="izin" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="send" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <small>Total sakit</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Sakit
                                            </h6>
                                            <div id="sakit" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="activity" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <small>Total masuk</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Hari Libur
                                            </h6>
                                            <div id="work-holiday" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="calendar" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <small>Jumlah wajib masuk</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Hari
                                            </h6>
                                            <div id="hari" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="calendar" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="card card-border mb-0 h-100 pt-2 pb-0">
                                <div class="card-body pt-3 pb-0">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <small>Total belum masuk</small>
                                            <h6 class="text-xs text-primary mb-0">
                                                Alpa / Belum
                                            </h6>
                                            <div id="alpa" class="mb-0 fw-bold text-dark" style="font-size:2.3rem;">-</div>
                                        </div>
                                        <div class="col-auto">
                                            <span class="feather-icon">
                                                <i data-feather="x-circle" style="color: #eaeaea; font-size:45px;"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 g-md-3">
                <div class="col-12">
                    <h4 class="fw-bold mb-0">
                        Kalendar Absensi
                    </h4>
                    <p class="fs-7">H = Hadir, I = Izin, S = Sakit, A = Alpa</p>
                </div>
                <div id="presence-calendar" class="row g-2 g-md-3">
                    <div class="col-12">
                        <div class="card card-border">
                            <div class="card-body">
                                Silahkan filter karyawan terlebih dahulu.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#check-tahun').change(function(e){
        e.preventDefault()
        if ($('#check-tahun').is(":checked")){
            $('#filter-bulan').hide()
            $('#select-month').removeAttr('name')
            $('#filter-tahun').show()
            $('#filter-absensi').attr('action','<?= base_url('absensi/get_laporan_year') ?>')
        } else {
            $('#filter-absensi').attr('action','<?= base_url('absensi/get_laporan') ?>')
            $('#filter-bulan').show()
            $('#select-month').attr('name','filter_month')
            $('#filter-tahun').hide()
        }
    })
    $(document).ready(function () {

        $(document).on('submit','#filter-absensi', function(e){
            e.preventDefault()
            const url = $(this).attr('action')
            $.ajax({
                type: "POST",
                url: url, 
                data: $(this).serialize(),
                dataType: "json",  
                cache: false,
                success: function(response){
                    if (response.success) {
                        $('#hadir').text(response.desc.hadir)
                        $('#izin').text(response.desc.izin)
                        $('#sakit').text(response.desc.sakit)
                        $('#hari').text(response.total_day)
                        $('#alpa').text(response.total_alpa)
                        $('#work-holiday').text(response.desc.work_on_holiday)
                        $('#csrf').val(response.csrfhash)
                        $('#presence-calendar').empty().append(response.calendar)
                    }
                    
                    if (response.error) {
                        sweatalert_confirm('danger', response)
                        // $('#csrf').val(response.csrfhash)
                    }

                    if (response.errors) {
                        sweatalert_confirm('danger', response)
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

        function sweatalert_confirm(type, res){
            let icon;
            let classHeader;
            if (type == 'success') {
                icon = '😍';
                classHeader = 'avatar-soft-success'
            } else if(type == 'danger'){
                icon = '😣';
                classHeader = 'avatar-soft-danger'
            } else if(type == 'warning'){
                icon = '🧐';
                classHeader = 'avatar-soft-warning'
            }

            Swal.fire({
                html:
                `<div class="avatar avatar-icon ${classHeader}  mb-3">
                    <span class="initial-wrap rounded-8">
                        ${icon}
                    </span>
                </div>
                <div>
                    <h5 class="text-dark fw-bold">${res.title}</h5>
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