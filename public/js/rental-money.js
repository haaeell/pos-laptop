(() => {
    const fields = new WeakMap();

    function format(input, finish = false) {
        const hidden = fields.get(input);
        const cursor = input.selectionStart ?? input.value.length;
        const charactersBeforeCursor = input.value
            .slice(0, cursor)
            .replace(/[^\d,]/g, "").length;
        const cleaned = input.value.replace(/[^\d,]/g, "");
        const [whole = "", ...fractions] = cleaned.split(",");
        const integer = whole.replace(/^0+(?=\d)/, "") || (cleaned ? "0" : "");
        let fraction = fractions.join("").slice(0, 2);
        const hasComma =
            cleaned.includes(",") && (!finish || fraction.length > 0);
        if (finish && fraction) fraction = fraction.padEnd(2, "0");

        hidden.value = integer
            ? integer + (fraction ? "." + fraction : "")
            : "";
        input.value = integer
            ? "Rp " +
              integer.replace(/\B(?=(\d{3})+(?!\d))/g, ".") +
              (hasComma ? "," + fraction : "")
            : "";

        if (document.activeElement === input && !finish) {
            let position = input.value ? 3 : 0;
            let seen = 0;
            while (
                position < input.value.length &&
                seen < charactersBeforeCursor
            ) {
                if (/[\d,]/.test(input.value[position])) seen++;
                position++;
            }
            input.setSelectionRange(position, position);
        }
        input.dispatchEvent(
            new CustomEvent("rupiah:change", { bubbles: true }),
        );
    }

    function init(root = document) {
        root.querySelectorAll("[data-rupiah]").forEach((input) => {
            if (fields.has(input)) return;
            const hidden = document.createElement("input");
            hidden.type = "hidden";
            hidden.name = input.name;
            input.removeAttribute("name");
            input.after(hidden);
            fields.set(input, hidden);

            // Values rendered by the server use a decimal point; typed amounts use Indonesian separators.
            const initial = input.value;
            if (/^\d+(\.\d{1,2})?$/.test(initial)) {
                const [whole, fraction] = initial.split(".");
                input.value =
                    whole +
                    (fraction && Number(fraction) ? "," + fraction : "");
            }
            format(input, true);
            input.addEventListener("input", () => format(input));
            input.addEventListener("blur", () => format(input, true));
        });
    }

    window.RentalMoney = {
        init,
        value: (input) => fields.get(input)?.value ?? input.value,
    };
    init();
})();
