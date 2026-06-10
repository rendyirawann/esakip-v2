<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;

AppAsset::register($this);
?>



<footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
    <!--  -->
    <div class="row">
      <div class="col-sm-6 my-1">
        <p class="m-0">Copyright &copy; <?= date('Y') ?>. eSakip. Sistem Akuntabilitas Kinerja Instansi Pemerintah <a href="https://bappedalitbang.deliserdangkab.go.id/" target="_blank"> Bappedalitbang Deli Serdang</a></p>
      </div>
      <div class="col-sm-6 ms-auto my-1">
        <ul class="list-inline footer-link mb-0 justify-content-sm-end d-flex">
          <li class="list-inline-item">Version APP 2.0</li>
          <!-- <li class="list-inline-item"><a href="https://pcoded.gitbook.io/light-able/" target="_blank">Documentation</a></li>
              <li class="list-inline-item"><a href="https://phoenixcoded.support-hub.io/" target="_blank">Support</a></li> -->
        </ul>
      </div>
    </div>
  </div>
</footer>
<div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
  <div class="offcanvas-header justify-content-between">
    <h5 class="offcanvas-title">Settings</h5>
    <button type="button" class="btn btn-icon btn-link-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
  </div>
  <div class="pct-body customizer-body">
    <div class="offcanvas-body py-0">
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <div class="pc-dark">
            <h6 class="mb-1">Theme Mode</h6>
            <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
            <div class="row theme-color theme-layout">
              <div class="col-4">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="true" onclick="layout_change('light');">
                    <span class="btn-label">Light</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');">
                    <span class="btn-label">Dark</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="default" onclick="layout_change_default();" data-bs-toggle="tooltip" title="Automatically sets the theme based on user's operating system's color scheme.">
                    <span class="btn-label">Default</span>
                    <span class="pc-lay-icon d-flex align-items-center justify-content-center">
                      <i class="ph-duotone ph-cpu"></i>
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Sidebar Theme</h6>
          <p class="text-muted text-sm">Choose Sidebar Theme</p>
          <div class="row theme-color theme-sidebar-color">
            <div class="col-6">
              <div class="d-grid">
                <button class="preset-btn btn" data-value="true" onclick="layout_sidebar_change('dark');">
                  <span class="btn-label">Dark</span>
                  <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                </button>
              </div>
            </div>
            <div class="col-6">
              <div class="d-grid">
                <button class="preset-btn btn active" data-value="false" onclick="layout_sidebar_change('light');">
                  <span class="btn-label">Light</span>
                  <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                </button>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Accent color</h6>
          <p class="text-muted text-sm">Choose your primary theme color</p>
          <div class="theme-color preset-color">
            <a href="#!" class="active" data-value="preset-1"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-2"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-3"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-4"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-5"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-6"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-7"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-8"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-9"><i class="ti ti-check"></i></a>
            <a href="#!" data-value="preset-10"><i class="ti ti-check"></i></a>
          </div>
        </li>
        <li class="list-group-item">
          <h6 class="mb-1">Sidebar Caption</h6>
          <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
          <div class="row theme-color theme-nav-caption">
            <div class="col-6">
              <div class="d-grid">
                <button class="preset-btn btn active" data-value="true" onclick="layout_caption_change('true');">
                  <span class="btn-label">Caption Show</span>
                  <span class="pc-lay-icon"><span></span><span></span><span><span></span><span></span></span><span></span></span>
                </button>
              </div>
            </div>
            <div class="col-6">
              <div class="d-grid">
                <button class="preset-btn btn" data-value="false" onclick="layout_caption_change('false');">
                  <span class="btn-label">Caption Hide</span>
                  <span class="pc-lay-icon"><span></span><span></span><span><span></span><span></span></span><span></span></span>
                </button>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="pc-rtl">
            <h6 class="mb-1">Theme Layout</h6>
            <p class="text-muted text-sm">LTR/RTL</p>
            <div class="row theme-color theme-direction">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="false" onclick="layout_rtl_change('false');">
                    <span class="btn-label">LTR</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="true" onclick="layout_rtl_change('true');">
                    <span class="btn-label">RTL</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item pc-box-width">
          <div class="pc-container-width">
            <h6 class="mb-1">Layout Width</h6>
            <p class="text-muted text-sm">Choose Full or Container Layout</p>
            <div class="row theme-color theme-container">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="false" onclick="change_box_container('false')">
                    <span class="btn-label">Full Width</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span><span></span></span></span>
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="true" onclick="change_box_container('true')">
                    <span class="btn-label">Fixed Width</span>
                    <span class="pc-lay-icon"><span></span><span></span><span></span><span><span></span></span></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="d-grid">
            <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var elements = document.querySelectorAll('[data-trigger]');
    elements.forEach(function(element) {
      new Choices(element, {
        placeholderValue: 'This is a placeholder set in the config',
        searchPlaceholderValue: 'Search for an option'
      });
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var genericExamples = document.querySelectorAll('[data-trigger]');
    for (i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        placeholderValue: 'This is a placeholder set in the config',
        searchPlaceholderValue: 'This is a search placeholder'
      });
    }

    var textRemove = new Choices(document.getElementById('choices-text-remove-button'), {
      delimiter: ',',
      editItems: true,
      maxItemCount: 5,
      removeItemButton: true
    });

    var text_Unique_Val = new Choices('#choices-text-unique-values', {
      paste: false,
      duplicateItemsAllowed: false,
      editItems: true
    });

    var text_i18n = new Choices('#choices-text-i18n', {
      paste: false,
      duplicateItemsAllowed: false,
      editItems: true,
      maxItemCount: 5,
      addItemText: function(value) {
        return 'Appuyez sur Entrée pour ajouter <b>"' + String(value) + '"</b>';
      },
      maxItemText: function(maxItemCount) {
        return String(maxItemCount) + 'valeurs peuvent être ajoutées';
      },
      uniqueItemText: 'Cette valeur est déjà présente'
    });

    var textEmailFilter = new Choices('#choices-text-email-filter', {
      editItems: true,
      addItemFilter: function(value) {
        if (!value) {
          return false;
        }

        const regex =
          /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        const expression = new RegExp(regex.source, 'i');
        return expression.test(value);
      }
    }).setValue(['joe@bloggs.com']);

    var textDisabled = new Choices('#choices-text-disabled', {
      addItems: false,
      removeItems: false
    }).disable();

    var textPrependAppendVal = new Choices('#choices-text-prepend-append-value', {
      prependValue: 'item-',
      appendValue: '-' + Date.now()
    }).removeActiveItems();

    var textPresetVal = new Choices('#choices-text-preset-values', {
      items: [
        'Josh Johnson',
        {
          value: 'joe@bloggs.co.uk',
          label: 'Joe Bloggs',
          customProperties: {
            description: 'Joe Blogg is such a generic name'
          }
        }
      ]
    });

    var multipleDefault = new Choices(document.getElementById('choices-multiple-groups'));

    var multipleFetch = new Choices('#choices-multiple-remote-fetch', {
      placeholder: true,
      placeholderValue: 'Pick an Strokes record',
      maxItemCount: 5
    }).setChoices(function() {
      return fetch('https://api.discogs.com/artists/55980/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
        .then(function(response) {
          return response.json();
        })
        .then(function(data) {
          return data.releases.map(function(release) {
            return {
              value: release.title,
              label: release.title
            };
          });
        });
    });

    var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
      removeItemButton: true
    });

    /* Use label on event */
    var choicesSelect = new Choices('#choices-multiple-labels', {
      removeItemButton: true,
      choices: [{
          value: 'One',
          label: 'Label One'
        },
        {
          value: 'Two',
          label: 'Label Two',
          disabled: true
        },
        {
          value: 'Three',
          label: 'Label Three'
        }
      ]
    }).setChoices(
      [{
          value: 'Four',
          label: 'Label Four',
          disabled: true
        },
        {
          value: 'Five',
          label: 'Label Five'
        },
        {
          value: 'Six',
          label: 'Label Six',
          selected: true
        }
      ],
      'value',
      'label',
      false
    );

    choicesSelect.passedElement.element.addEventListener('addItem', function(event) {
      document.getElementById('message').innerHTML =
        '<span class="badge bg-light-primary"> You just added "' + event.detail.label + '"</span>';
    });
    choicesSelect.passedElement.element.addEventListener('removeItem', function(event) {
      document.getElementById('message').innerHTML =
        '<span class="badge bg-light-danger"> You just removed "' + event.detail.label + '"</span>';
    });

    var singleFetch = new Choices('#choices-single-remote-fetch', {
        searchPlaceholderValue: 'Search for an Arctic Monkeys record'
      })
      .setChoices(function() {
        return fetch('https://api.discogs.com/artists/391170/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            return data.releases.map(function(release) {
              return {
                label: release.title,
                value: release.title
              };
            });
          });
      })
      .then(function(instance) {
        instance.setChoiceByValue('Fake Tales Of San Francisco');
      });

    var singleXhrRemove = new Choices('#choices-single-remove-xhr', {
      removeItemButton: true,
      searchPlaceholderValue: "Search for a Smiths' record"
    }).setChoices(function(callback) {
      return fetch('https://api.discogs.com/artists/83080/releases?token=QBRmstCkwXEvCjTclCpumbtNwvVkEzGAdELXyRyW')
        .then(function(res) {
          return res.json();
        })
        .then(function(data) {
          return data.releases.map(function(release) {
            return {
              label: release.title,
              value: release.title
            };
          });
        });
    });

    var singleNoSearch = new Choices('#choices-single-no-search', {
      searchEnabled: false,
      removeItemButton: true,
      choices: [{
          value: 'One',
          label: 'Label One'
        },
        {
          value: 'Two',
          label: 'Label Two',
          disabled: true
        },
        {
          value: 'Three',
          label: 'Label Three'
        }
      ]
    }).setChoices(
      [{
          value: 'Four',
          label: 'Label Four',
          disabled: true
        },
        {
          value: 'Five',
          label: 'Label Five'
        },
        {
          value: 'Six',
          label: 'Label Six',
          selected: true
        }
      ],
      'value',
      'label',
      false
    );

    var singlePresetOpts = new Choices('#choices-single-preset-options', {
      placeholder: true
    }).setChoices(
      [{
          label: 'Group one',
          id: 1,
          disabled: false,
          choices: [{
              value: 'Child One',
              label: 'Child One',
              selected: true
            },
            {
              value: 'Child Two',
              label: 'Child Two',
              disabled: true
            },
            {
              value: 'Child Three',
              label: 'Child Three'
            }
          ]
        },
        {
          label: 'Group two',
          id: 2,
          disabled: false,
          choices: [{
              value: 'Child Four',
              label: 'Child Four',
              disabled: true
            },
            {
              value: 'Child Five',
              label: 'Child Five'
            },
            {
              value: 'Child Six',
              label: 'Child Six'
            }
          ]
        }
      ],
      'value',
      'label'
    );

    var singleSelectedOpt = new Choices('#choices-single-selected-option', {
      searchFields: ['label', 'value', 'customProperties.description'],
      choices: [{
          value: 'One',
          label: 'Label One',
          selected: true
        },
        {
          value: 'Two',
          label: 'Label Two',
          disabled: true
        },
        {
          value: 'Three',
          label: 'Label Three',
          customProperties: {
            description: 'This option is fantastic'
          }
        }
      ]
    }).setChoiceByValue('Two');

    var customChoicesPropertiesViaDataAttributes = new Choices('#choices-with-custom-props-via-html', {
      searchFields: ['label', 'value', 'customProperties']
    });

    var singleNoSorting = new Choices('#choices-single-no-sorting', {
      shouldSort: false
    });

    var cities = new Choices(document.getElementById('cities'));
    var tubeStations = new Choices(document.getElementById('tube-stations')).disable();

    cities.passedElement.element.addEventListener('change', function(e) {
      if (e.detail.value === 'London') {
        tubeStations.enable();
      } else {
        tubeStations.disable();
      }
    });

    var customTemplates = new Choices(document.getElementById('choices-single-custom-templates'), {
      callbackOnCreateTemplates: function(strToEl) {
        var classNames = this.config.classNames;
        var itemSelectText = this.config.itemSelectText;
        return {
          item: function(classNames, data) {
            return strToEl(
              '\
                                <div\
                                class="' +
              String(classNames.item) +
              ' ' +
              String(data.highlighted ? classNames.highlightedState : classNames.itemSelectable) +
              '"\
                                data-item\
                                data-id="' +
              String(data.id) +
              '"\
                                data-value="' +
              String(data.value) +
              '"\
                                ' +
              String(data.active ? 'aria-selected="true"' : '') +
              '\
                                ' +
              String(data.disabled ? 'aria-disabled="true"' : '') +
              '\
                                >\
                                <span style="margin-right:10px;">🎉</span> ' +
              String(data.label) +
              '\
                                </div>\
                                '
            );
          },
          choice: function(classNames, data) {
            return strToEl(
              '\
                                <div\
                                class="' +
              String(classNames.item) +
              ' ' +
              String(classNames.itemChoice) +
              ' ' +
              String(data.disabled ? classNames.itemDisabled : classNames.itemSelectable) +
              '"\
                                data-select-text="' +
              String(itemSelectText) +
              '"\
                                data-choice \
                                ' +
              String(data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable') +
              '\
                                data-id="' +
              String(data.id) +
              '"\
                                data-value="' +
              String(data.value) +
              '"\
                                ' +
              String(data.groupId > 0 ? 'role="treeitem"' : 'role="option"') +
              '\
                                >\
                                <span style="margin-right:10px;">👉🏽</span> ' +
              String(data.label) +
              '\
                                </div>\
                                '
            );
          }
        };
      }
    });

    var resetSimple = new Choices(document.getElementById('reset-simple'));

    var resetMultiple = new Choices('#reset-multiple', {
      removeItemButton: true
    });
  });
</script>

<script>
  // [ base style ]
  $('#base-style').DataTable();

  // [ no style ]
  $('#no-style').DataTable();

  // [ compact style ]
  $('#compact').DataTable();

  // // [ hover style ]
  // $('#table-style-hover').DataTable();
</script>

<script>
  // [ DOM/jquery ]
  var total, pageTotal;
  var table = $('#dom-jqry').DataTable();
  // [ column Rendering ]
  $('#colum-render').DataTable({
    columnDefs: [{
        render: function(data, type, row) {
          return data + ' (' + row[3] + ')';
        },
        targets: 0
      },
      {
        visible: false,
        targets: [3]
      }
    ]
  });
  // [ Multiple Table Control Elements ]
  $('#multi-table').DataTable({
    dom: '<"top"iflp<"clear">>rt<"bottom"iflp<"clear">>'
  });
  // [ Complex Headers With Column Visibility ]
  $('#complex-header').DataTable({
    columnDefs: [{
      visible: false,
      targets: -1
    }]
  });
  // [ Language file ]
  $('#lang-file').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json'
    }
  });
  // [ Setting Defaults ]
  $('#setting-default').DataTable();
  // [ Row Grouping ]
  var table1 = $('#row-grouping').DataTable({
    columnDefs: [{
      visible: false,
      targets: 2
    }],
    order: [
      [2, 'asc']
    ],
    displayLength: 25,
    drawCallback: function(settings) {
      var api = this.api();
      var rows = api
        .rows({
          page: 'current'
        })
        .nodes();
      var last = null;

      api
        .column(2, {
          page: 'current'
        })
        .data()
        .each(function(group, i) {
          if (last !== group) {
            $(rows)
              .eq(i)
              .before('<tr class="group"><td colspan="5">' + group + '</td></tr>');

            last = group;
          }
        });
    }
  });
  // [ Order by the grouping ]
  $('#row-grouping tbody').on('click', 'tr.group', function() {
    var currentOrder = table.order()[0];
    if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
      table.order([2, 'desc']).draw();
    } else {
      table.order([2, 'asc']).draw();
    }
  });
  // [ Footer callback ]
  $('#footer-callback').DataTable({
    footerCallback: function(row, data, start, end, display) {
      var api = this.api(),
        data;

      // Remove the formatting to get integer data for summation
      var intVal = function(i) {
        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
      };

      // Total over all pages
      total = api
        .column(4)
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      // Total over this page
      pageTotal = api
        .column(4, {
          page: 'current'
        })
        .data()
        .reduce(function(a, b) {
          return intVal(a) + intVal(b);
        }, 0);

      // Update footer
      $(api.column(4).footer()).html('$' + pageTotal + ' ( $' + total + ' total)');
    }
  });
  // [ Custom Toolbar Elements ]
  $('#c-tool-ele').DataTable({
    dom: '<"toolbar">frtip'
  });
  // [ Custom Toolbar Elements ]
  $('div.toolbar').html('<b>Custom tool bar! Text/images etc.</b>');
  // [ custom callback ]
  $('#row-callback').DataTable({
    createdRow: function(row, data, index) {
      if (data[5].replace(/[\$,]/g, '') * 1 > 150000) {
        $('td', row).eq(5).addClass('highlight');
      }
    }
  });
</script>
<!-- [Page Specific JS] end -->

<script>
  // [ Configuration Option ]
  $('#res-config').DataTable({
    responsive: true
  });

  // [ New Constructor ]
  var newcs = $('#new-cons').DataTable();

  new $.fn.dataTable.Responsive(newcs);

  // [ Immediately Show Hidden Details ]
  $('#show-hide-res').DataTable({
    responsive: {
      details: {
        display: $.fn.dataTable.Responsive.display.childRowImmediate,
        type: ''
      }
    }
  });
</script>
<!-- [Page Specific JS] end -->

<!-- 
    <script>layout_change('light');</script>
  
    <script>layout_sidebar_change('light');</script>
    
    
    
    <script>change_box_container('false');</script>
    
    
    <script>layout_caption_change('true');</script>
    
    
    
    
    <script>layout_rtl_change('false');</script>
    
    
    <script>preset_change("preset-1");</script> -->