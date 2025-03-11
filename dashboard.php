<!--breadcrumb-->

      <div class="page-breadcrumb mt-4 d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Dashboard</div>
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">Payroll</li>
            </ol>
          </nav>
        </div>
        
      </div>
      <!--end breadcrumb-->


      <div class="row">
        
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
          <div class="card rounded-4 w-100">
            <div class="card-body">
              <div class="mb-3 d-flex align-items-center justify-content-between">
                <div
                  class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary">
                  <span class="material-icons-outlined fs-5">account_balance_wallet</span>
                </div>
                <div>
                  <!-- <span class="text-success d-flex align-items-center">+24%<i
                      class="material-icons-outlined">expand_less</i></span> -->
                </div>
              </div>
              <div>
                <h4 class="mb-0 company_balance">$00</h4>
                <p class="mb-3">Company Balance</p>
                <div id="chart1"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
          <div class="card rounded-4 w-100">
            <div class="card-body">
              <div class="mb-3 d-flex align-items-center justify-content-between">
                <div
                  class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success">
                  <span class="material-icons-outlined fs-5">attach_money</span>
                </div>
                <div>
                  <!-- <span class="text-success d-flex align-items-center">+14%<i
                      class="material-icons-outlined">expand_less</i></span> -->
                </div>
              </div>
              <div>
                <h4 class="mb-0 total_expenses">$00</h4>
                <p class="mb-3">Company Expense</p>
                <div id="chart2"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
          <div class="card rounded-4 w-100">
            <div class="card-body">
              <div class="mb-3 d-flex align-items-center justify-content-between">
                <div
                  class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10 text-info">
                  <span class="material-icons-outlined fs-5">payments</span>
                </div>
                <div>
                  <!-- <span class="text-danger d-flex align-items-center">-35%<i
                      class="material-icons-outlined">expand_less</i></span> -->
                </div>
              </div>
              <div>
                <h4 class="mb-0 upcoming_salary">$00</h4>
                <p class="mb-3">Upcoming salary amount</p>
                <div id="chart3"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-3 col-xxl-3 d-flex">
          <div class="card rounded-4 w-100">
            <div class="card-body">
              <div class="mb-3 d-flex align-items-center justify-content-between">
                <div
                  class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning">
                  <span class="material-icons-outlined fs-5">calendar_month</span>
                </div>
                <div>
                  <!-- <span class="text-success d-flex align-items-center">+18%<i
                      class="material-icons-outlined">expand_less</i></span> -->
                </div>
              </div>
              <div>
                <h4 class="mb-0 coming_date"><?=date('F d, Y');?></h4>
                <p class="mb-3">Upcoming salary date</p>
                <div id="chart4"></div>
              </div>
            </div>
          </div>
        </div>

      </div><!--end row-->


      <div class="row">
        <div class="col-12 col-xl-4">
          <div class="card w-100 rounded-4">
            <div class="card-body">
              <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-start justify-content-between">
                  <div class="">
                    <h5 class="mb-0">Employment Status</h5>
                  </div>
                  <!-- <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div> -->
                </div>
                
                <div class="d-flex flex-column gap-3">
                  <?php 
                  $totalEmployeesQuery = "SELECT COUNT(*) AS total_employees FROM employees";
                  $totalResult = $GLOBALS['conn']->query($totalEmployeesQuery);
                  $totalEmployees = $totalResult->fetch_assoc()['total_employees'];

                  ?>
                  <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0 d-flex align-items-center gap-2 w-40"><span
                        class="material-icons-outlined fs-6 text-primary">fiber_manual_record</span>Total Employees</p>
                    <div class="">
                      <p class="mb-0"><?=$totalEmployees;?></p>
                    </div>
                  </div>
                  <?php 
                  $contractTypesQuery = "SELECT ct.`name` AS 'contract_type', COUNT(e.`employee_id`) AS 'employee_count' FROM `contract_types` ct LEFT JOIN `employees` e ON e.`contract_type` = ct.`name` GROUP BY ct.`id`";
                  $result = $GLOBALS['conn']->query($contractTypesQuery);
                  while ($row = $result->fetch_assoc()) {
                    $contractType = $row['contract_type'];
                    $employeeCount = $row['employee_count'];
                    $percentage = $totalEmployees > 0 ? round(($employeeCount / $totalEmployees) * 100, 2) : 0;

                  ?>
                  <div class="d-flex sflex border p-3 rounded-4 align-items-center justify-content-between">
                    <p class="mb-0 d-flex align-items-center gap-2 sflex-basis-40"><span
                        class="material-icons-outlined fs-6  text-primary">fiber_manual_record</span><?=$contractType;?></p>
                    <div class=" sflex-basis-20">
                      <p class="mb-0 " style="text-align: right;"><?=$employeeCount;?></p>
                    </div>

                    <div class="sflex-basis-20">
                      <p class="mb-0 w-20" style="text-align: right;"><?=$percentage;?>%</p>
                    </div>
                  </div>
                <?php } ?>
                  
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-xl-8">
          <div class="card w-100 rounded-4">
            <div class="card-body" style="height: 343px;">
              <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="">
                  <h5 class="mb-0">Sales & Views</h5>
                </div>
                <!-- <div class="dropdown">
                  <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                    data-bs-toggle="dropdown">
                    <span class="material-icons-outlined fs-5">more_vert</span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                    <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                    <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                  </ul>
                </div> -->
              </div>
              <div id="chart5"></div>
              <!-- <div
                class="d-flex flex-column flex-lg-row align-items-start justify-content-around border p-3 rounded-4 mt-3 gap-3">
                <div class="d-flex align-items-center gap-4">
                  <div class="">
                    <p class="mb-0 data-attributes">
                      <span
                        data-peity='{ "fill": ["#2196f3", "rgb(255 255 255 / 12%)"], "innerRadius": 32, "radius": 40 }'>5/7</span>
                    </p>
                  </div>
                  <div class="">
                    <p class="mb-1 fs-6 fw-bold">Monthly</p>
                    <h2 class="mb-0">65,127</h2>
                    <p class="mb-0"><span class="text-success me-2 fw-medium">16.5%</span><span>55.21 USD</span></p>
                  </div>
                </div>
                <div class="vr"></div>
                <div class="d-flex align-items-center gap-4">
                  <div class="">
                    <p class="mb-0 data-attributes">
                      <span
                        data-peity='{ "fill": ["#ffd200", "rgb(255 255 255 / 12%)"], "innerRadius": 32, "radius": 40 }'>5/7</span>
                    </p>
                  </div>
                  <div class="">
                    <p class="mb-1 fs-6 fw-bold">Yearly</p>
                    <h2 class="mb-0">984,246</h2>
                    <p class="mb-0"><span class="text-success me-2 fw-medium">24.9%</span><span>267.35 USD</span></p>
                  </div>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </div><!--end row-->


      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <p>Pending payroll</p>
            <table id="payrollDT" class="table table-striped table-bordered" style="width:100%">
              <thead>
                <tr role="row">
                  <th>Staff No. </th>
                  <th>Full name </th>
                  <th>Gross salary </th>
                  <th>Earnings </th>
                  <th>Deductions </th>
                  <th>Tax </th>
                  <th>Net salary </th>
                  <th>Status </th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $month = date('Y-m');
                $query = "SELECT `id`, `payroll_id`, `emp_id`, `staff_no`, `full_name`, `status`, `base_salary`, (`allowance` + `bonus` + `commission`) AS earnings, (`loan` + `advance` + `deductions`) AS `total_deductions`, `tax`, (`base_salary` + (`allowance` + `bonus` + `commission`) - (`loan` + `advance` + `deductions`) - `tax`) AS net_salary FROM `payroll_details` WHERE  `status` IN ('Approved', 'Pending') AND `month` LIKE '$month%' LIMIT 10";
                $data = $GLOBALS['conn']->query($query);
                while($row = $data->fetch_assoc()) { ?>

                  <tr role="row">
                    <td><?=$row['staff_no'];?> </td>
                    <td><?=$row['full_name'];?></td>
                    <td><?=formatMoney($row['base_salary']);?> </td>
                    <td><?=formatMoney($row['earnings']);?> </td>
                    <td><?=formatMoney($row['total_deductions']);?> </td>
                    <td><?=formatMoney($row['tax']);?> </td>
                    <td><?=formatMoney($row['net_salary']);?> </td>
                    <td><?=$row['status'];?> </td>
                  </tr>

               <?php  }

                ?>
              </tbody>
            </table> 
          </div>
        </div>
      </div>

      <script src="<?=baseUri();?>/assets/js/modules/dashboard.js"></script>
      <script type="text/javascript">
        
      </script>