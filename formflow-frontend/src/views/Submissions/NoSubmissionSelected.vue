<template>
    <div class="no-submission-selected">
        <div class="row">
            <div class="col-md-10 col-xl-8 mx-auto text-center">
                <img src="@/assets/images/submission-details.svg" alt="">
                <h4>Select a submission to view more details</h4>
                <p>Discover additional details by clicking on a submission of your choice</p>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "NoSubmissionSelectedView",
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.no-submission-selected {
    height: 100svh;
    background: $background-grey;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 1.5rem;

    @include tablet {
        height: calc(100svh - 56px);
    }

    img {
        width: 15rem;
        height: 15rem;
    }

    h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: $dark;
        margin: 1rem 0;
    }

    p {
        color: $grey-text;
        font-size: 0.9rem;
    }
}
</style>