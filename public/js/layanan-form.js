document.addEventListener("DOMContentLoaded", function () {
    const userSelect = document.getElementById("user_id");
    const unitPelaksanaInput = document.getElementById("unit_pelaksana");
    const namaPicInput = document.getElementById("nama_pic");
    const emailPicInput = document.getElementById("email_pic");
    const hpTelpPicInput = document.getElementById("hp_telp_pic");

    // Check if we have the users data from the page
    if (typeof window.usersData === "undefined") {
        console.log("Users data not found");
        return;
    }

    const usersData = window.usersData;

    if (userSelect && unitPelaksanaInput) {
        console.log("Form elements found, setting up event listener");
        console.log("Available users:", usersData);

        // Update fields when OPD is selected
        userSelect.addEventListener("change", function () {
            const selectedUserId = this.value;
            console.log("Selected User ID:", selectedUserId);

            if (selectedUserId && usersData[selectedUserId]) {
                const userData = usersData[selectedUserId];
                console.log("User Data:", userData);

                // Fill the fields
                unitPelaksanaInput.value = userData.nama_opd || "";
                if (namaPicInput) namaPicInput.value = userData.name || "";
                if (emailPicInput) emailPicInput.value = userData.email || "";
                if (hpTelpPicInput) hpTelpPicInput.value = userData.no_hp || "";

                console.log("Fields updated successfully");
            } else {
                // Clear the fields
                unitPelaksanaInput.value = "";
                if (namaPicInput) namaPicInput.value = "";
                if (emailPicInput) emailPicInput.value = "";
                if (hpTelpPicInput) hpTelpPicInput.value = "";

                console.log("Fields cleared");
            }
        });

        // Set initial values if editing and user is already selected
        const currentUserId = userSelect.value;
        if (currentUserId && usersData[currentUserId]) {
            const userData = usersData[currentUserId];

            // Only fill if fields are empty (to avoid overwriting existing data)
            if (!unitPelaksanaInput.value)
                unitPelaksanaInput.value = userData.nama_opd || "";
            if (namaPicInput && !namaPicInput.value)
                namaPicInput.value = userData.name || "";
            if (emailPicInput && !emailPicInput.value)
                emailPicInput.value = userData.email || "";
            if (hpTelpPicInput && !hpTelpPicInput.value)
                hpTelpPicInput.value = userData.no_hp || "";

            console.log("Initial values set for editing");
        }
    } else {
        console.log("Form elements not found");
    }
});
