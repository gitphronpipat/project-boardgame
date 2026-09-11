$(document).ready(function () {
  $('#Table').DataTable({

    // ภาษาไทย
    language: {
      url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
    },

    // ============ จำเป็นสำหรับตารางปกติ ============

    pageLength: 10,           // แถวต่อหน้า
    lengthMenu: [10, 25, 50,100], // ตัวเลือก dropdown
    searching: true,          // ✅ ค้นหา
    ordering: true,           // ✅ เรียงลำดับ
    order: [[0, 'asc']],      // เรียง column แรกตอนโหลด
    paging: true,             // แบ่งหน้า
    info: true,               // "แสดง 1-10 จาก 50"
    responsive: true,         // รองรับมือถือ
    autoWidth: false,         // ปิด — จัดความกว้างเอง

		columnDefs: [	
			// เปิด reset ทุก column

			// ยกเว้น column สุดท้าย (ปุ่มจัดการ) ปิดเรียงเลย
			{ targets: -1, orderable: false },
		],

  });
});


// ============ ฟังก์ชันเสริม (ยังไม่ได้ใส่) ============
//
// scrollX: true
//   — scroll แนวนอนเมื่อ column เยอะ
//
// scrollY: "400px"
//   — ล็อคความสูงตาราง scroll แนวตั้ง
//
// dom: 'Bfrtip'  +  buttons: ['copy','csv','excel','pdf','print']
//   — ปุ่ม export (ต้องโหลด Buttons extension เพิ่ม)
//
// ajax: 'api/data.json'
//   — โหลดข้อมูลจาก server แทนใช้ HTML
//
// serverSide: true
//   — ให้ server จัดการ search/sort/page (ใช้คู่ ajax, ข้อมูลเป็นแสน)
//
// processing: true
//   — แสดง "กำลังโหลด..." ระหว่างรอ ajax
//
// stateSave: true
//   — จำสถานะตาราง (หน้า, การเรียง, ค้นหา) แม้ refresh
//
// createdRow: function(row, data, index) { ... }
//   — callback แต่ละแถวตอนสร้าง ใช้เพิ่ม class หรือ style เอง
