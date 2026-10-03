(() => {
    const {
        products,
        tiers,
        items: savedItems,
        dates: savedDates,
    } = window.rentalFormData;
    const items = document.getElementById("items");
    const dates = document.getElementById("dateList");
    const form = document.getElementById("rentalForm");
    const plannedReturn = document.getElementById("plannedReturn");
    const money = (value) =>
        "Rp " +
        Number(value).toLocaleString("id-ID", { maximumFractionDigits: 2 });
    let nextItem = 0;
    let nextDate = 0;

    function addItem(value = {}) {
        const row = document
            .getElementById("rentalItemTemplate")
            .content.firstElementChild.cloneNode(true);
        const index = nextItem++;
        const select = row.querySelector("select");
        select.name = `items[${index}][product_id]`;
        select.add(new Option("", ""));
        products.forEach((product) =>
            select.add(
                new Option(
                    `${product.name} | ${product.product_code} | stok ${product.stock}`,
                    product.id,
                ),
            ),
        );
        select.value = value.product_id || "";
        [
            [".qty", "qty", 1],
            [".accessories", "accessories", "Laptop dan Charger"],
            [".condition-out", "condition_out", "Normal"],
        ].forEach(([selector, field, fallback]) => {
            const input = row.querySelector(selector);
            input.name = `items[${index}][${field}]`;
            input.value = value[field] ?? fallback;
        });
        items.append(row);
        $(select)
            .select2({
                width: "100%",
                placeholder: "Cari nama atau kode laptop",
                language: { noResults: () => "Laptop tidak ditemukan" },
            })
            .on("change", calculate);
        row.querySelector(".remove-item").addEventListener("click", () => {
            $(select).select2("destroy");
            row.remove();
            calculate();
        });
        calculate();
    }

    function addDate(value = "") {
        const row = document.createElement("div");
        row.className = "flex gap-2";
        const input = document.createElement("input");
        input.type = "date";
        input.required = true;
        input.name = `rental_dates[${nextDate++}]`;
        input.className =
            "date w-full p-2.5 border border-slate-200 rounded-xl text-sm";
        input.setAttribute("aria-label", "Tanggal pemakaian");
        input.value = value;
        const remove = document.createElement("button");
        remove.type = "button";
        remove.className = "px-3 text-rose-500";
        remove.textContent = "×";
        remove.setAttribute("aria-label", "Hapus tanggal");
        remove.addEventListener("click", () => {
            row.remove();
            calculate();
        });
        row.append(input, remove);
        dates.append(row);
        calculate();
    }

    function calculate() {
        const dateInputs = [...dates.querySelectorAll("input")];
        const chosenDates = dateInputs
            .map((input) => input.value)
            .filter(Boolean);
        const uniqueDates = [...new Set(chosenDates)].sort();
        const days = uniqueDates.length;
        dateInputs.forEach((input) =>
            input.setCustomValidity(
                input.value &&
                    chosenDates.filter((date) => date === input.value).length >
                        1
                    ? "Tanggal sewa tidak boleh sama."
                    : "",
            ),
        );
        plannedReturn.min = uniqueDates.at(-1) || "";
        const selected = [...items.children].map((row) => ({
            row,
            product: products.find(
                (product) =>
                    String(product.id) === row.querySelector("select").value,
            ),
            qty: Number(row.querySelector(".qty").value),
        }));
        let invalidStock = false;
        let duplicateProduct = false;
        selected.forEach(({ row, product, qty }) => {
            const qtyInput = row.querySelector(".qty");
            const duplicate =
                product &&
                selected.filter((item) => item.product?.id === product.id)
                    .length > 1;
            duplicateProduct ||= Boolean(duplicate);
            const unavailable =
                product &&
                selected
                    .filter((item) => item.product?.id === product.id)
                    .reduce((sum, item) => sum + item.qty, 0) > product.stock;
            invalidStock ||= Boolean(unavailable);
            if (product) qtyInput.max = product.stock;
            else qtyInput.removeAttribute("max");
            qtyInput.setCustomValidity(
                duplicate
                    ? "Laptop sudah dipilih. Gabungkan jumlah unit pada satu baris."
                    : unavailable
                      ? "Jumlah melebihi stok tersedia."
                      : "",
            );
        });
        const validItems = selected.filter(
            (item) =>
                item.product && Number.isInteger(item.qty) && item.qty > 0,
        );
        const qty = validItems.reduce((sum, item) => sum + item.qty, 0);
        const tier = [...tiers]
            .sort((a, b) => b.min_qty - a.min_qty)
            .find((tier) => qty >= tier.min_qty);
        const rate = Number(tier?.daily_rate || 0);
        const costRows = document.getElementById("costRows");
        costRows.replaceChildren();
        validItems.forEach((item) => {
            const tr = document.createElement("tr");
            [
                item.product.name,
                item.qty,
                days,
                tier ? money(rate) : "Belum diatur",
                tier ? money(item.qty * days * rate) : "—",
            ].forEach((value, index) => {
                const td = document.createElement("td");
                td.className =
                    index === 0
                        ? "px-5 py-3 font-medium text-slate-700"
                        : "px-4 py-3 text-right whitespace-nowrap";
                td.textContent = value;
                tr.append(td);
            });
            costRows.append(tr);
        });
        if (!validItems.length) {
            const td = document.createElement("td");
            td.colSpan = 5;
            td.className = "p-5 text-center text-slate-400";
            td.textContent =
                "Pilih laptop dan jumlah unit untuk melihat rincian biaya.";
            const tr = document.createElement("tr");
            tr.append(td);
            costRows.append(tr);
        }
        document.getElementById("tierInfo").textContent = tier
            ? `Tarif untuk minimal ${tier.min_qty} unit: ${money(rate)} / unit / hari, berlaku untuk seluruh ${qty} unit.`
            : "Tarif belum tersedia untuk jumlah unit yang dipilih.";
        document.getElementById("summary").textContent =
            `${qty} unit × ${days} hari × ${money(rate)}`;
        document.getElementById("total").textContent = tier
            ? money(qty * days * rate)
            : "—";
        const issue = duplicateProduct
            ? "Laptop yang sama cukup dipilih sekali; sesuaikan jumlah unitnya."
            : invalidStock
              ? "Jumlah unit melebihi stok tersedia."
              : chosenDates.length !== days
                ? "Tanggal sewa tidak boleh sama."
                : !selected.length || validItems.length !== selected.length
                  ? "Pilih laptop dan isi jumlah unit minimal 1."
                  : !days || chosenDates.length !== dateInputs.length
                    ? "Lengkapi tanggal pemakaian untuk menghitung biaya."
                    : !tier
                      ? "Atur tarif untuk jumlah unit yang dipilih terlebih dahulu."
                      : plannedReturn.value &&
                          plannedReturn.value < uniqueDates.at(-1)
                        ? "Rencana pengembalian tidak boleh sebelum tanggal sewa terakhir."
                        : "";
        document.getElementById("formIssue").textContent = issue;
        document.getElementById("saveRental").disabled = Boolean(issue);
    }

    document
        .getElementById("addItem")
        .addEventListener("click", () => addItem());
    document
        .getElementById("addDate")
        .addEventListener("click", () => addDate());
    form.addEventListener("input", calculate);
    form.addEventListener("change", calculate);
    form.addEventListener("submit", (event) => {
        calculate();
        if (document.getElementById("saveRental").disabled)
            event.preventDefault();
    });
    savedDates.forEach(addDate);
    savedItems.forEach(addItem);
})();
