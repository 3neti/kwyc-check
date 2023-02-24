<!DOCTYPE html>
<html lang="en">

<head>
    <script
        src="https://hv-camera-web-sg.s3.ap-southeast-1.amazonaws.com/hyperverge-web-sdk%40demo/src/sdk.min.js"></script>
</head>

<body>
<div>
    <button type="button" onclick="starOnboarding();">
        Start Onboarding
    </button>
</div>
</body>
<script>
    function starOnboarding() {
        const accessToken = "{{ $access_token }}"
        const hyperKycConfig = new HyperKycConfig(
            accessToken,
            "default",
            "{{ $transaction_id }}"
        );
        HyperKYCModule.launch(hyperKycConfig, handler);
    }
    const handler = (HyperKycResult) => {
        if (HyperKycResult.Cancelled) {
            // user cancelled
            console.log(HyperKycResult.Cancelled);
        } else if (HyperKycResult.Failure) {
            // failure
            console.log(HyperKycResult.Failure);
        } else if (HyperKycResult.Success) {
            // success
            console.log(HyperKycResult.Success);
        }
    }
</script>

</html>
