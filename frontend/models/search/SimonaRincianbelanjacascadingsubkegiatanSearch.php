<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SimonaRincianbelanjacascadingsubkegiatan;

/**
 * SimonaRincianbelanjacascadingsubkegiatanSearch represents the model behind the search form of `frontend\models\SimonaRincianbelanjacascadingsubkegiatan`.
 */
class SimonaRincianbelanjacascadingsubkegiatanSearch extends SimonaRincianbelanjacascadingsubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonarincianbelanjacascadingsubkegiatan_id', 'refsimonacascadingsubkegiatan_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id', 'refskpd_id', 'refperiode_id'], 'integer'],
            [['detail_rincianbelanja', 'satuan_rincianbelanja', 'jumlah_rincianbelanja', 'anggaran_rincianbelanja'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SimonaRincianbelanjacascadingsubkegiatan::find();

        // Eager load relations for performance
        $query->with(['refProgram', 'refKegiatan', 'refSubkegiatan']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refsimonarincianbelanjacascadingsubkegiatan_id' => $this->refsimonarincianbelanjacascadingsubkegiatan_id,
            'refsimonacascadingsubkegiatan_id' => $this->refsimonacascadingsubkegiatan_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
        ]);

        $query->andFilterWhere(['like', 'detail_rincianbelanja', $this->detail_rincianbelanja])
            ->andFilterWhere(['like', 'satuan_rincianbelanja', $this->satuan_rincianbelanja])
            ->andFilterWhere(['like', 'jumlah_rincianbelanja', $this->jumlah_rincianbelanja])
            ->andFilterWhere(['like', 'anggaran_rincianbelanja', $this->anggaran_rincianbelanja]);

        return $dataProvider;
    }
}
